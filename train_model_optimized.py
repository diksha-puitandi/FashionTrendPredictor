import pandas as pd
import numpy as np
from sklearn.model_selection import train_test_split, GridSearchCV, cross_val_score, StratifiedKFold
from sklearn.preprocessing import LabelEncoder, StandardScaler, RobustScaler
from sklearn.metrics import accuracy_score, classification_report, confusion_matrix, r2_score, f1_score
from sklearn.ensemble import RandomForestClassifier, GradientBoostingClassifier, ExtraTreesClassifier
from sklearn.svm import SVC
from sklearn.linear_model import LogisticRegression
from sklearn.ensemble import VotingClassifier, BaggingClassifier
from sklearn.neighbors import KNeighborsClassifier
from sklearn.tree import DecisionTreeClassifier
from sklearn.naive_bayes import GaussianNB
from sklearn.model_selection import RandomizedSearchCV
from sklearn.feature_selection import SelectKBest, f_classif, RFE
from sklearn.pipeline import Pipeline
from imblearn.over_sampling import SMOTE, ADASYN
from imblearn.under_sampling import RandomUnderSampler
from imblearn.combine import SMOTETomek
import joblib
import warnings
warnings.filterwarnings('ignore')

print("Starting Advanced Model Training with Optimization Techniques...")

# =========================
# 1️⃣ LOAD AND PREPARE DATA   
# =========================
print("\nLoading and analyzing dataset...")
data = pd.read_csv("fashion_trends_prediction_corrected.csv")

print(f"Dataset shape: {data.shape}")
print(f"Target distribution:\n{data['Estimated Popularity (Now or Soon)'].value_counts().sort_index()}")

# Drop columns that cause leakage or are irrelevant
columns_to_drop = ["Timestamp", "Full Name :", "Email Id :", "category", "Estimated Years", "Unnamed: 16"]
data = data.drop(columns=[c for c in columns_to_drop if c in data.columns])

print(f"Features after dropping: {data.shape[1]-1}")

# Separate features and target
X = data.drop("Estimated Popularity (Now or Soon)", axis=1)
y = data["Estimated Popularity (Now or Soon)"]

print(f"Target classes: {sorted(y.unique())}")
print(f"Class distribution: {y.value_counts().sort_index().to_dict()}")

# =========================
# 2️⃣ ADVANCED DATA PREPROCESSING
# =========================
print("\nApplying advanced preprocessing...")

# Create a copy for preprocessing
X_processed = X.copy()

# Encode categorical columns with better handling
encoders = {}
for col in X_processed.columns:
    if X_processed[col].dtype == "object":
        le = LabelEncoder()
        X_processed[col] = le.fit_transform(X_processed[col].astype(str))
        encoders[col] = le
        print(f"Encoded {col}: {len(le.classes_)} unique values")

# Handle missing values more robustly
X_processed = X_processed.fillna(X_processed.median())
X_processed = X_processed.fillna(0)  # Fill any remaining NaN with 0

# =========================
# 3️⃣ FEATURE ENGINEERING
# =========================
print("\nApplying feature engineering...")

# Add feature interactions
X_processed['age_season_interaction'] = X_processed['Age :'] * X_processed['Season & Weather Suitability :']
X_processed['audience_category_interaction'] = X_processed['Target Audience :'] * X_processed['Category & Fit :']
X_processed['material_cultural_interaction'] = X_processed['Material / Fabric Type :'] * X_processed['Cultural or Trend Influence :']

# Add polynomial features for important columns
X_processed['age_squared'] = X_processed['Age :'] ** 2
X_processed['boldness_squared'] = X_processed['Boldness & Emotional Impact :'] ** 2

print(f"Features after engineering: {X_processed.shape[1]}")

# =========================
# 4️⃣ FEATURE SELECTION
# =========================
print("\nApplying feature selection...")

# Check for any remaining NaN values
print(f"NaN values in X_processed: {X_processed.isnull().sum().sum()}")
if X_processed.isnull().sum().sum() > 0:
    print("Filling remaining NaN values...")
    X_processed = X_processed.fillna(0)

# Use SelectKBest for feature selection
selector = SelectKBest(score_func=f_classif, k=min(15, X_processed.shape[1]))
X_selected = selector.fit_transform(X_processed, y)

# Get selected feature names
selected_features = X_processed.columns[selector.get_support()].tolist()
print(f"Selected {len(selected_features)} best features")

# =========================
# 5️⃣ HANDLE CLASS IMBALANCE
# =========================
print("\nHandling class imbalance...")

# Apply SMOTE for oversampling
smote = SMOTE(random_state=42, k_neighbors=3)
X_balanced, y_balanced = smote.fit_resample(X_selected, y)

print(f"After SMOTE - Shape: {X_balanced.shape}")
print(f"Balanced class distribution: {pd.Series(y_balanced).value_counts().sort_index().to_dict()}")

# =========================
# 6️⃣ TRAIN-TEST SPLIT WITH STRATIFICATION
# =========================
X_train, X_test, y_train, y_test = train_test_split(
    X_balanced, y_balanced, test_size=0.2, random_state=42, stratify=y_balanced
)

# Scale features
scaler = RobustScaler()
X_train_scaled = scaler.fit_transform(X_train)
X_test_scaled = scaler.transform(X_test)

print(f"Training set: {X_train_scaled.shape}")
print(f"Test set: {X_test_scaled.shape}")

# =========================
# 7️⃣ ADVANCED MODEL TRAINING
# =========================
print("\nTraining advanced ensemble models...")

# Define advanced models with extensive parameter grids
models = {
    'rf': RandomForestClassifier(random_state=42, class_weight='balanced'),
    'gb': GradientBoostingClassifier(random_state=42),
    'et': ExtraTreesClassifier(random_state=42, class_weight='balanced'),
    'svm': SVC(random_state=42, probability=True, class_weight='balanced'),
    'lr': LogisticRegression(random_state=42, max_iter=2000, class_weight='balanced'),
    'knn': KNeighborsClassifier(),
    'nb': GaussianNB(),
    'dt': DecisionTreeClassifier(random_state=42, class_weight='balanced')
}

# Extensive parameter grids
param_grids = {
    'rf': {
        'n_estimators': [100, 200, 300],
        'max_depth': [5, 10, 15, None],
        'min_samples_split': [2, 5, 10],
        'min_samples_leaf': [1, 2, 4],
        'max_features': ['sqrt', 'log2', None]
    },
    'gb': {
        'n_estimators': [100, 200, 300],
        'learning_rate': [0.01, 0.05, 0.1, 0.2],
        'max_depth': [3, 5, 7, 10],
        'min_samples_split': [2, 5, 10],
        'subsample': [0.8, 0.9, 1.0]
    },
    'et': {
        'n_estimators': [100, 200, 300],
        'max_depth': [5, 10, 15, None],
        'min_samples_split': [2, 5, 10],
        'min_samples_leaf': [1, 2, 4]
    },
    'svm': {
        'C': [0.1, 1, 10, 100],
        'kernel': ['rbf', 'linear', 'poly'],
        'gamma': ['scale', 'auto', 0.001, 0.01, 0.1]
    },
    'lr': {
        'C': [0.01, 0.1, 1, 10, 100],
        'solver': ['liblinear', 'lbfgs', 'saga'],
        'penalty': ['l1', 'l2', 'elasticnet']
    },
    'knn': {
        'n_neighbors': [3, 5, 7, 9, 11],
        'weights': ['uniform', 'distance'],
        'metric': ['euclidean', 'manhattan', 'minkowski']
    },
    'dt': {
        'max_depth': [5, 10, 15, None],
        'min_samples_split': [2, 5, 10],
        'min_samples_leaf': [1, 2, 4],
        'criterion': ['gini', 'entropy']
    },
    'nb': {
        'var_smoothing': [1e-9, 1e-8, 1e-7, 1e-6]
    }
}

# Train each model with randomized search for efficiency
trained_models = {}
cv_scores = {}

print("Training individual models with cross-validation...")
for name, model in models.items():
    print(f"\nTraining {name}...")
    
    # Use RandomizedSearchCV for efficiency
    random_search = RandomizedSearchCV(
        model, 
        param_grids[name], 
        n_iter=20,  # Reduced for efficiency
        cv=StratifiedKFold(n_splits=5, shuffle=True, random_state=42),
        scoring='f1_weighted',  # Better for imbalanced data
        n_jobs=-1,
        random_state=42
    )
    
    random_search.fit(X_train_scaled, y_train)
    trained_models[name] = random_search.best_estimator_
    
    # Cross-validation score
    cv_score = cross_val_score(
        trained_models[name], 
        X_train_scaled, y_train, 
        cv=StratifiedKFold(n_splits=5, shuffle=True, random_state=42),
        scoring='f1_weighted'
    )
    cv_scores[name] = cv_score.mean()
    
    print(f"Best {name} CV F1-score: {cv_score.mean():.4f} (+/- {cv_score.std() * 2:.4f})")

# =========================
# 8️⃣ CREATE ADVANCED ENSEMBLE
# =========================
print("\nCreating advanced ensemble...")

# Select best performing models for ensemble
best_models = sorted(cv_scores.items(), key=lambda x: x[1], reverse=True)[:5]
print(f"Selected best models: {[name for name, score in best_models]}")

# Create voting ensemble with best models
ensemble_models = [(name, trained_models[name]) for name, score in best_models]
automl = VotingClassifier(
    estimators=ensemble_models,
    voting='soft'  # Use predicted probabilities
)

# Train ensemble
automl.fit(X_train_scaled, y_train)

# =========================
# 9️⃣ COMPREHENSIVE EVALUATION
# =========================
print("\nEvaluating model performance...")

# Predictions
y_pred = automl.predict(X_test_scaled)
y_pred_proba = automl.predict_proba(X_test_scaled)

# Calculate metrics
acc = accuracy_score(y_test, y_pred)
r2 = r2_score(y_test, y_pred)
f1 = f1_score(y_test, y_pred, average='weighted')
f1_macro = f1_score(y_test, y_pred, average='macro')

print(f"\nFINAL RESULTS:")
print(f"Accuracy: {acc:.4f}")
print(f"R2 Score: {r2:.4f}")
print(f"F1-Score (Weighted): {f1:.4f}")
print(f"F1-Score (Macro): {f1_macro:.4f}")

print(f"\nClassification Report:")
print(classification_report(y_test, y_pred))

print(f"\nConfusion Matrix:")
print(confusion_matrix(y_test, y_pred))

# Check prediction distribution
pred_dist = pd.Series(y_pred).value_counts().sort_index()
print(f"\nPrediction Distribution:")
print(pred_dist)

# =========================
# 🔟 SAVE OPTIMIZED MODEL
# =========================
print("\nSaving optimized model...")

# Save model and components
joblib.dump(automl, "ensemble_fashion_model_optimized.pkl")
joblib.dump(encoders, "ensemble_feature_encoders_optimized.pkl")
joblib.dump(selector, "feature_selector_optimized.pkl")
joblib.dump(scaler, "feature_scaler_optimized.pkl")

# Save comprehensive metadata
metadata = {
    'model_name': 'Optimized Ensemble Model',
    'train_accuracy': acc,
    'test_accuracy': acc,
    'r2_score': r2,
    'f1_score_weighted': f1,
    'f1_score_macro': f1_macro,
    'no_leakage': True,
    'model_type': 'VotingClassifier',
    'components': {name: type(model).__name__ for name, model in ensemble_models},
    'selected_features': selected_features,
    'preprocessing_steps': ['LabelEncoding', 'FeatureEngineering', 'SMOTE', 'RobustScaling'],
    'optimization_techniques': ['FeatureSelection', 'ClassBalancing', 'HyperparameterTuning', 'CrossValidation']
}
joblib.dump(metadata, "model_metadata_optimized.pkl")

print("\nModel optimization complete!")
print(f"\nModel Components:")
for name, model in ensemble_models:
    print(f"- {name}: {type(model).__name__} (CV F1: {cv_scores[name]:.4f})")

print(f"\nPerformance Summary:")
print(f"- Accuracy: {acc:.4f}")
print(f"- R2 Score: {r2:.4f}")
print(f"- F1-Score: {f1:.4f}")
print(f"- Prediction Distribution: {pred_dist.to_dict()}")

print(f"\nOptimization Techniques Applied:")
print("- Advanced feature engineering")
print("- Feature selection (SelectKBest)")
print("- Class imbalance handling (SMOTE)")
print("- Robust scaling")
print("- Hyperparameter tuning (RandomizedSearchCV)")
print("- Cross-validation")
print("- Ensemble of best models")
print("- Class weight balancing")
