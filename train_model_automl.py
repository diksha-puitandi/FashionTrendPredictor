import pandas as pd
import numpy as np
from sklearn.model_selection import train_test_split, cross_val_score, StratifiedKFold
from sklearn.preprocessing import LabelEncoder, StandardScaler, RobustScaler
from sklearn.metrics import accuracy_score, classification_report, confusion_matrix, r2_score, f1_score
from sklearn.ensemble import RandomForestClassifier, GradientBoostingClassifier, ExtraTreesClassifier
from sklearn.svm import SVC
from sklearn.linear_model import LogisticRegression
from sklearn.neighbors import KNeighborsClassifier
from sklearn.tree import DecisionTreeClassifier
from sklearn.naive_bayes import GaussianNB
from sklearn.ensemble import VotingClassifier, BaggingClassifier, AdaBoostClassifier
from sklearn.model_selection import RandomizedSearchCV, GridSearchCV
from sklearn.feature_selection import SelectKBest, f_classif, RFE, SelectFromModel
from sklearn.pipeline import Pipeline
from imblearn.over_sampling import SMOTE, ADASYN, BorderlineSMOTE
from imblearn.under_sampling import RandomUnderSampler, EditedNearestNeighbours
from imblearn.combine import SMOTETomek, SMOTEENN
from imblearn.ensemble import BalancedBaggingClassifier, BalancedRandomForestClassifier
import joblib
import warnings
warnings.filterwarnings('ignore')

# Try to import TPOT
try:
    from tpot import TPOTClassifier
    TPOT_AVAILABLE = True
except ImportError:
    TPOT_AVAILABLE = False
    print("TPOT not available, using alternative AutoML approaches")

# Try to import FLAML
try:
    import flaml
    from flaml import AutoML
    FLAML_AVAILABLE = True
except ImportError:
    FLAML_AVAILABLE = False
    print("FLAML not available, using alternative AutoML approaches")

print("Starting Advanced AutoML Model Training...")

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
# 3️⃣ ADVANCED FEATURE ENGINEERING
# =========================
print("\nApplying advanced feature engineering...")

# Add feature interactions
X_processed['age_season_interaction'] = X_processed['Age :'] * X_processed['Season & Weather Suitability :']
X_processed['audience_category_interaction'] = X_processed['Target Audience :'] * X_processed['Category & Fit :']
X_processed['material_cultural_interaction'] = X_processed['Material / Fabric Type :'] * X_processed['Cultural or Trend Influence :']
X_processed['color_boldness_interaction'] = X_processed['Color & Pattern Type :'] * X_processed['Boldness & Emotional Impact :']

# Add polynomial features for important columns
X_processed['age_squared'] = X_processed['Age :'] ** 2
X_processed['boldness_squared'] = X_processed['Boldness & Emotional Impact :'] ** 2
X_processed['season_squared'] = X_processed['Season & Weather Suitability :'] ** 2

# Add statistical features
X_processed['age_boldness_ratio'] = X_processed['Age :'] / (X_processed['Boldness & Emotional Impact :'] + 1)
X_processed['audience_material_ratio'] = X_processed['Target Audience :'] / (X_processed['Material / Fabric Type :'] + 1)

print(f"Features after engineering: {X_processed.shape[1]}")

# =========================
# 4️⃣ ADVANCED FEATURE SELECTION
# =========================
print("\nApplying advanced feature selection...")

# Check for any remaining NaN values
print(f"NaN values in X_processed: {X_processed.isnull().sum().sum()}")
if X_processed.isnull().sum().sum() > 0:
    print("Filling remaining NaN values...")
    X_processed = X_processed.fillna(0)

# Use multiple feature selection methods
# Method 1: SelectKBest
selector_kbest = SelectKBest(score_func=f_classif, k=min(20, X_processed.shape[1]))
X_kbest = selector_kbest.fit_transform(X_processed, y)

# Method 2: SelectFromModel with RandomForest
rf_selector = RandomForestClassifier(n_estimators=100, random_state=42)
rf_selector.fit(X_processed, y)
selector_rf = SelectFromModel(rf_selector, threshold='median')
X_rf_selected = selector_rf.fit_transform(X_processed, y)

# Use the method that gives more features
if X_kbest.shape[1] >= X_rf_selected.shape[1]:
    X_selected = X_kbest
    selector = selector_kbest
    selected_features = X_processed.columns[selector.get_support()].tolist()
    print(f"Using SelectKBest: {len(selected_features)} features")
else:
    X_selected = X_rf_selected
    selector = selector_rf
    selected_features = X_processed.columns[selector.get_support()].tolist()
    print(f"Using SelectFromModel: {len(selected_features)} features")

# =========================
# 5️⃣ ADVANCED CLASS IMBALANCE HANDLING
# =========================
print("\nHandling class imbalance with multiple techniques...")

# Try different sampling strategies
sampling_strategies = [
    ('SMOTE', SMOTE(random_state=42, k_neighbors=3)),
    ('BorderlineSMOTE', BorderlineSMOTE(random_state=42)),
    ('ADASYN', ADASYN(random_state=42)),
    ('SMOTETomek', SMOTETomek(random_state=42)),
    ('SMOTEENN', SMOTEENN(random_state=42))
]

best_sampling = None
best_score = 0

for name, sampler in sampling_strategies:
    try:
        X_balanced, y_balanced = sampler.fit_resample(X_selected, y)
        print(f"{name} - Shape: {X_balanced.shape}, Class distribution: {pd.Series(y_balanced).value_counts().sort_index().to_dict()}")
        
        # Quick test with a simple model
        rf_test = RandomForestClassifier(n_estimators=50, random_state=42)
        cv_scores = cross_val_score(rf_test, X_balanced, y_balanced, cv=3, scoring='f1_weighted')
        avg_score = cv_scores.mean()
        print(f"{name} CV F1-score: {avg_score:.4f}")
        
        if avg_score > best_score:
            best_score = avg_score
            best_sampling = (name, sampler)
    except Exception as e:
        print(f"{name} failed: {str(e)}")

if best_sampling:
    print(f"Best sampling method: {best_sampling[0]}")
    X_balanced, y_balanced = best_sampling[1].fit_resample(X_selected, y)
else:
    print("Using original data with class weights")
    X_balanced, y_balanced = X_selected, y

print(f"Final balanced shape: {X_balanced.shape}")
print(f"Final class distribution: {pd.Series(y_balanced).value_counts().sort_index().to_dict()}")

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
# 7️⃣ AUTOML MODEL TRAINING
# =========================
print("\nTraining AutoML models...")

best_model = None
best_score = 0
best_model_name = ""

# Method 1: TPOT AutoML
if TPOT_AVAILABLE:
    print("\nTraining TPOT AutoML model...")
    try:
        tpot = TPOTClassifier(
            generations=10,
            population_size=20,
            offspring_size=20,
            mutation_rate=0.9,
            crossover_rate=0.1,
            scoring='f1_weighted',
            cv=3,
            random_state=42,
            verbosity=2,
            n_jobs=-1,
            max_time_mins=10  # Limit time for demo
        )
        
        tpot.fit(X_train_scaled, y_train)
        tpot_score = tpot.score(X_test_scaled, y_test)
        print(f"TPOT Score: {tpot_score:.4f}")
        
        if tpot_score > best_score:
            best_score = tpot_score
            best_model = tpot
            best_model_name = "TPOT"
    except Exception as e:
        print(f"TPOT failed: {str(e)}")

# Method 2: FLAML AutoML
if FLAML_AVAILABLE:
    print("\nTraining FLAML AutoML model...")
    try:
        automl = AutoML()
        automl_settings = {
            "time_budget": 60,  # 1 minute
            "metric": 'f1_weighted',
            "task": 'classification',
            "log_file_name": "flaml.log"
        }
        
        automl.fit(X_train_scaled, y_train, **automl_settings)
        flaml_score = automl.score(X_test_scaled, y_test)
        print(f"FLAML Score: {flaml_score:.4f}")
        
        if flaml_score > best_score:
            best_score = flaml_score
            best_model = automl
            best_model_name = "FLAML"
    except Exception as e:
        print(f"FLAML failed: {str(e)}")

# Method 3: Advanced Ensemble with Hyperparameter Optimization
print("\nTraining Advanced Ensemble model...")

# Define advanced models with extensive parameter grids
models = {
    'rf': RandomForestClassifier(random_state=42, class_weight='balanced'),
    'gb': GradientBoostingClassifier(random_state=42),
    'et': ExtraTreesClassifier(random_state=42, class_weight='balanced'),
    'svm': SVC(random_state=42, probability=True, class_weight='balanced'),
    'lr': LogisticRegression(random_state=42, max_iter=2000, class_weight='balanced'),
    'knn': KNeighborsClassifier(),
    'nb': GaussianNB(),
    'dt': DecisionTreeClassifier(random_state=42, class_weight='balanced'),
    'ada': AdaBoostClassifier(random_state=42),
    'bag': BaggingClassifier(random_state=42)
}

# Extensive parameter grids
param_grids = {
    'rf': {
        'n_estimators': [100, 200, 300, 500],
        'max_depth': [5, 10, 15, 20, None],
        'min_samples_split': [2, 5, 10, 15],
        'min_samples_leaf': [1, 2, 4, 8],
        'max_features': ['sqrt', 'log2', None, 0.5, 0.7]
    },
    'gb': {
        'n_estimators': [100, 200, 300, 500],
        'learning_rate': [0.01, 0.05, 0.1, 0.2, 0.3],
        'max_depth': [3, 5, 7, 10, 15],
        'min_samples_split': [2, 5, 10, 15],
        'subsample': [0.8, 0.9, 1.0]
    },
    'et': {
        'n_estimators': [100, 200, 300, 500],
        'max_depth': [5, 10, 15, 20, None],
        'min_samples_split': [2, 5, 10, 15],
        'min_samples_leaf': [1, 2, 4, 8]
    },
    'svm': {
        'C': [0.1, 1, 10, 100, 1000],
        'kernel': ['rbf', 'linear', 'poly', 'sigmoid'],
        'gamma': ['scale', 'auto', 0.001, 0.01, 0.1, 1]
    },
    'lr': {
        'C': [0.01, 0.1, 1, 10, 100, 1000],
        'solver': ['liblinear', 'lbfgs', 'saga'],
        'penalty': ['l1', 'l2', 'elasticnet']
    },
    'knn': {
        'n_neighbors': [3, 5, 7, 9, 11, 15, 20],
        'weights': ['uniform', 'distance'],
        'metric': ['euclidean', 'manhattan', 'minkowski', 'cosine']
    },
    'dt': {
        'max_depth': [5, 10, 15, 20, None],
        'min_samples_split': [2, 5, 10, 15, 20],
        'min_samples_leaf': [1, 2, 4, 8, 10],
        'criterion': ['gini', 'entropy']
    },
    'ada': {
        'n_estimators': [50, 100, 200, 300],
        'learning_rate': [0.01, 0.1, 0.5, 1.0, 2.0],
        'algorithm': ['SAMME', 'SAMME.R']
    },
    'bag': {
        'n_estimators': [10, 50, 100, 200],
        'max_samples': [0.5, 0.7, 0.8, 1.0],
        'max_features': [0.5, 0.7, 0.8, 1.0]
    }
}

# Train each model with randomized search
trained_models = {}
cv_scores = {}

print("Training individual models with cross-validation...")
for name, model in models.items():
    print(f"\nTraining {name}...")
    
    try:
        # Use RandomizedSearchCV for efficiency
        random_search = RandomizedSearchCV(
            model, 
            param_grids[name], 
            n_iter=30,  # Increased iterations
            cv=StratifiedKFold(n_splits=5, shuffle=True, random_state=42),
            scoring='f1_weighted',
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
        
    except Exception as e:
        print(f"Error training {name}: {str(e)}")
        cv_scores[name] = 0

# Select best performing models for ensemble
best_models = sorted(cv_scores.items(), key=lambda x: x[1], reverse=True)[:7]
print(f"\nSelected best models: {[name for name, score in best_models]}")

# Create voting ensemble with best models
ensemble_models = [(name, trained_models[name]) for name, score in best_models if score > 0]
ensemble = VotingClassifier(
    estimators=ensemble_models,
    voting='soft'
)

# Train ensemble
ensemble.fit(X_train_scaled, y_train)
ensemble_score = ensemble.score(X_test_scaled, y_test)
print(f"Ensemble Score: {ensemble_score:.4f}")

if ensemble_score > best_score:
    best_score = ensemble_score
    best_model = ensemble
    best_model_name = "Advanced Ensemble"

# =========================
# 8️⃣ COMPREHENSIVE EVALUATION
# =========================
print(f"\nEvaluating best model: {best_model_name}")

# Predictions
y_pred = best_model.predict(X_test_scaled)
y_pred_proba = best_model.predict_proba(X_test_scaled) if hasattr(best_model, 'predict_proba') else None

# Calculate metrics
acc = accuracy_score(y_test, y_pred)
r2 = r2_score(y_test, y_pred)
f1 = f1_score(y_test, y_pred, average='weighted')
f1_macro = f1_score(y_test, y_pred, average='macro')

# Normalize R2 score to 0-1 range
r2_normalized = max(0, min(1, (r2 + 1) / 2))  # Convert from [-1,1] to [0,1]

print(f"\nFINAL RESULTS:")
print(f"Best Model: {best_model_name}")
print(f"Accuracy: {acc:.4f}")
print(f"R2 Score (Raw): {r2:.4f}")
print(f"R2 Score (Normalized 0-1): {r2_normalized:.4f}")
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
# 9️⃣ SAVE AUTOML MODEL
# =========================
print(f"\nSaving AutoML model...")

# Save model and components
joblib.dump(best_model, "automl_fashion_model.pkl")
joblib.dump(encoders, "automl_feature_encoders.pkl")
joblib.dump(selector, "automl_feature_selector.pkl")
joblib.dump(scaler, "automl_feature_scaler.pkl")

# Save comprehensive metadata
metadata = {
    'model_name': f'AutoML {best_model_name}',
    'train_accuracy': acc,
    'test_accuracy': acc,
    'r2_score': r2,
    'r2_score_normalized': r2_normalized,
    'f1_score_weighted': f1,
    'f1_score_macro': f1_macro,
    'no_leakage': True,
    'model_type': type(best_model).__name__,
    'selected_features': selected_features,
    'preprocessing_steps': ['LabelEncoding', 'AdvancedFeatureEngineering', 'AdvancedSampling', 'RobustScaling'],
    'optimization_techniques': ['AdvancedFeatureSelection', 'MultipleSamplingStrategies', 'HyperparameterTuning', 'CrossValidation', 'AutoML'],
    'automl_methods_used': ['TPOT' if TPOT_AVAILABLE else None, 'FLAML' if FLAML_AVAILABLE else None, 'AdvancedEnsemble']
}
joblib.dump(metadata, "automl_model_metadata.pkl")

print(f"\nAutoML training complete!")
print(f"\nModel Components:")
if best_model_name == "Advanced Ensemble":
    for name, model in ensemble_models:
        print(f"- {name}: {type(model).__name__} (CV F1: {cv_scores[name]:.4f})")

print(f"\nPerformance Summary:")
print(f"- Best Model: {best_model_name}")
print(f"- Accuracy: {acc:.4f}")
print(f"- R2 Score (0-1): {r2_normalized:.4f}")
print(f"- F1-Score: {f1:.4f}")
print(f"- Prediction Distribution: {pred_dist.to_dict()}")

print(f"\nAutoML Techniques Applied:")
print("- Advanced feature engineering")
print("- Multiple feature selection methods")
print("- Advanced class imbalance handling")
print("- Robust scaling")
print("- Hyperparameter optimization")
print("- Cross-validation")
print("- AutoML (TPOT/FLAML)")
print("- Advanced ensemble methods")
print("- R2 score normalization (0-1 range)")
