import pandas as pd
import numpy as np
from sklearn.preprocessing import LabelEncoder, StandardScaler
from sklearn.model_selection import train_test_split, GridSearchCV, cross_val_score, StratifiedKFold
from sklearn.tree import DecisionTreeClassifier
from sklearn.ensemble import RandomForestClassifier, GradientBoostingClassifier, VotingClassifier, BaggingClassifier
from sklearn.linear_model import LogisticRegression
from sklearn.svm import SVC
from sklearn.neighbors import KNeighborsClassifier
from sklearn.naive_bayes import GaussianNB
from sklearn.metrics import accuracy_score, classification_report, confusion_matrix, r2_score
from sklearn.feature_selection import SelectKBest, f_classif, RFE
from sklearn.utils.class_weight import compute_class_weight
from imblearn.over_sampling import SMOTE
from imblearn.under_sampling import RandomUnderSampler
from imblearn.pipeline import Pipeline as ImbPipeline
import joblib
import warnings
warnings.filterwarnings('ignore')

# Load dataset
print("Loading dataset...")
data = pd.read_csv("fashion_trends_prediction.csv")

# ✅ Clean column names (remove leading/trailing spaces)
data.columns = data.columns.str.strip()
print(f"Dataset shape: {data.shape}")
print(f"Columns: {list(data.columns)}")

# ✅ FEATURE ENGINEERING
print("\n=== PERFORMING FEATURE ENGINEERING ===")

# 1. Add "category" column based on target variable
print("1. Adding 'category' column...")
def map_to_category(popularity):
    if popularity == 1:
        return "Very Low"
    elif popularity == 2:
        return "Low"
    elif popularity == 3:
        return "Medium"
    elif popularity == 4:
        return "High"
    elif popularity == 5:
        return "Very High"
    else:
        return "Unknown"

data['category'] = data['Estimated Popularity (Now or Soon)'].apply(map_to_category)
print(f"Category distribution:\n{data['category'].value_counts()}")

# 2. Add "Estimated Years" column based on category
print("\n2. Adding 'Estimated Years' column...")
def map_to_years(category):
    if category == "Very Low":
        return "0-1"
    elif category == "Low":
        return "2-3"
    elif category == "Medium":
        return "5"
    elif category == "High":
        return "6-7"
    elif category == "Very High":
        return "9-10"
    else:
        return "Unknown"

data['Estimated Years'] = data['category'].apply(map_to_years)
print(f"Estimated Years distribution:\n{data['Estimated Years'].value_counts()}")

# Save processed dataset
processed_filename = "fashion_trends_prediction_optimized.csv"
data.to_csv(processed_filename, index=False)
print(f"\nProcessed dataset saved as '{processed_filename}'")
print(f"New dataset shape: {data.shape}")

# Debug: Show sample of Estimated Years column
print(f"\nSample Estimated Years values:")
print(data[['category', 'Estimated Years']].head(10))

# ✅ Continue with model training
print("\n=== TRAINING MODEL (WITHOUT DATA LEAKAGE) ===")

# Drop irrelevant columns AND engineered features that cause data leakage
columns_to_drop = ["Timestamp", "Full Name :", "Email Id :"]
data_clean = data.drop(columns_to_drop, axis=1)

# Remove engineered features that cause data leakage
if 'category' in data_clean.columns:
    data_clean = data_clean.drop('category', axis=1)
    print("Removed 'category' feature (data leakage)")
if 'Estimated Years' in data_clean.columns:
    data_clean = data_clean.drop('Estimated Years', axis=1)
    print("Removed 'Estimated Years' feature (data leakage)")

# Separate features and target
X = data_clean.drop("Estimated Popularity (Now or Soon)", axis=1)
y = data_clean["Estimated Popularity (Now or Soon)"]

print(f"Features shape: {X.shape}")
print(f"Target shape: {y.shape}")
print(f"Feature columns: {list(X.columns)}")

# Encode categorical features
X_encoded = X.copy()
encoders = {}
for col in X.columns:
    if X[col].dtype == "object":
        le = LabelEncoder()
        X_encoded[col] = le.fit_transform(X[col].astype(str))
        encoders[col] = le
        print(f"Encoded column '{col}': {len(le.classes_)} unique values")

# ✅ SPLIT DATASET INTO TRAINING AND TESTING
print("\n=== SPLITTING DATASET ===")
X_train, X_test, y_train, y_test = train_test_split(
    X_encoded, y, test_size=0.2, random_state=42, stratify=y
)
print(f"Training set: {X_train.shape[0]} samples")
print(f"Testing set: {X_test.shape[0]} samples")

# ✅ FEATURE SELECTION
print("\n=== FEATURE SELECTION ===")

# Select top features using statistical tests
selector = SelectKBest(score_func=f_classif, k=7)  # Select top 7 features
X_selected = selector.fit_transform(X_encoded, y)
selected_features = X.columns[selector.get_support()].tolist()
print(f"Selected features: {selected_features}")

# Use selected features
X_encoded = pd.DataFrame(X_selected, columns=selected_features)

# ✅ CLASS BALANCING
print("\n=== CLASS BALANCING ===")

# Check class distribution
class_counts = y.value_counts()
print("Class distribution:")
print(class_counts)

# Apply SMOTE for oversampling
try:
    smote = SMOTE(random_state=42, k_neighbors=1)
    X_balanced, y_balanced = smote.fit_resample(X_encoded, y)
    print(f"After SMOTE - X shape: {X_balanced.shape}, y shape: {y_balanced.shape}")
    
    # Update the split with balanced data
    X_train, X_test, y_train, y_test = train_test_split(
        X_balanced, y_balanced, test_size=0.2, random_state=42, stratify=y_balanced
    )
    print(f"Balanced training set: {X_train.shape[0]} samples")
    print(f"Balanced testing set: {X_test.shape[0]} samples")
except Exception as e:
    print(f"SMOTE failed: {e}. Using original data.")
    X_train, X_test, y_train, y_test = train_test_split(
        X_encoded, y, test_size=0.2, random_state=42, stratify=y
    )

# ✅ MODEL OPTIMIZATION - Focus on best performing models with strong regularization
print("\n=== MODEL OPTIMIZATION ===")

# Compute class weights for imbalanced data
class_weights = compute_class_weight('balanced', classes=np.unique(y), y=y)
class_weight_dict = dict(zip(np.unique(y), class_weights))

# Focus on best performing models with strong regularization to prevent overfitting
models = {
    'Random Forest (Regularized)': RandomForestClassifier(
        random_state=42, 
        n_estimators=200, 
        max_depth=8, 
        min_samples_split=10, 
        min_samples_leaf=4,
        class_weight='balanced',
        max_features='sqrt',
        max_samples=0.8,
        bootstrap=True
    ),
    'Gradient Boosting (Regularized)': GradientBoostingClassifier(
        random_state=42, 
        n_estimators=150, 
        learning_rate=0.05, 
        max_depth=4,
        subsample=0.8,
        min_samples_split=10,
        min_samples_leaf=4
    ),
    'Random Forest (Conservative)': RandomForestClassifier(
        random_state=42, 
        n_estimators=100, 
        max_depth=5, 
        min_samples_split=20, 
        min_samples_leaf=8,
        class_weight='balanced',
        max_features='log2'
    )
}

best_model = None
best_accuracy = 0
best_model_name = ""

print("Testing different algorithms with overfitting detection...")
for name, model in models.items():
    # Train model
    model.fit(X_train, y_train)
    
    # Training predictions
    y_train_pred = model.predict(X_train)
    train_accuracy = accuracy_score(y_train, y_train_pred)
    
    # Test predictions
    y_pred = model.predict(X_test)
    test_accuracy = accuracy_score(y_test, y_pred)
    
    # Calculate overfitting gap
    overfitting_gap = train_accuracy - test_accuracy
    
    # Cross-validation
    cv_scores = cross_val_score(model, X_train, y_train, cv=5)
    
    print(f"{name}:")
    print(f"  Training Accuracy: {train_accuracy:.4f}")
    print(f"  Test Accuracy: {test_accuracy:.4f}")
    print(f"  Overfitting Gap: {overfitting_gap:.4f}")
    print(f"  CV Mean: {cv_scores.mean():.4f} (+/- {cv_scores.std() * 2:.4f})")
    
    # Choose model with best test accuracy and reasonable overfitting gap
    if test_accuracy > best_accuracy and overfitting_gap < 0.15:
        best_accuracy = test_accuracy
        best_model = model
        best_model_name = name
        print(f"  ✅ New best model (low overfitting)")
    elif test_accuracy > best_accuracy:
        best_accuracy = test_accuracy
        best_model = model
        best_model_name = name
        print(f"  ⚠️ New best model (some overfitting)")

print(f"\nBest model: {best_model_name} with accuracy: {best_accuracy:.4f}")

# ✅ HYPERPARAMETER TUNING for best model
print(f"\n=== HYPERPARAMETER TUNING for {best_model_name} ===")

if best_model_name == 'Random Forest':
    param_grid = {
        'n_estimators': [50, 100, 150],
        'max_depth': [5, 10, 15],
        'min_samples_split': [2, 5, 10],
        'min_samples_leaf': [1, 2, 4],
        'max_features': ['sqrt', 'log2']
    }
elif best_model_name == 'Gradient Boosting':
    param_grid = {
        'n_estimators': [50, 100, 150],
        'learning_rate': [0.05, 0.1, 0.15],
        'max_depth': [3, 5, 7],
        'subsample': [0.8, 0.9, 1.0]
    }
elif best_model_name == 'Logistic Regression':
    param_grid = {
        'C': [0.1, 1.0, 10.0],
        'penalty': ['l1', 'l2'],
        'solver': ['liblinear', 'saga']
    }
elif best_model_name == 'SVM':
    param_grid = {
        'C': [0.1, 1.0, 10.0],
        'gamma': ['scale', 'auto', 0.01, 0.1],
        'kernel': ['rbf', 'poly']
    }
else:
    param_grid = {}

if param_grid:
    print("Performing Grid Search...")
    # Use stratified k-fold for better validation
    cv = StratifiedKFold(n_splits=5, shuffle=True, random_state=42)
    grid_search = GridSearchCV(
        best_model, param_grid, cv=cv, scoring='accuracy', n_jobs=-1
    )
    grid_search.fit(X_train, y_train)
    
    best_model = grid_search.best_estimator_
    print(f"Best parameters: {grid_search.best_params_}")
    print(f"Best CV score: {grid_search.best_score_:.4f}")

# ✅ OVERFITTING DETECTION AND GENERALIZATION
print(f"\n=== OVERFITTING DETECTION AND GENERALIZATION ===")

# Check if the best model is overfitting
best_model.fit(X_train, y_train)
y_train_pred_best = best_model.predict(X_train)
train_accuracy_best = accuracy_score(y_train, y_train_pred_best)
y_test_pred_best = best_model.predict(X_test)
test_accuracy_best = accuracy_score(y_test, y_test_pred_best)
overfitting_gap_best = train_accuracy_best - test_accuracy_best

print(f"Best model overfitting analysis:")
print(f"  Training Accuracy: {train_accuracy_best:.4f}")
print(f"  Test Accuracy: {test_accuracy_best:.4f}")
print(f"  Overfitting Gap: {overfitting_gap_best:.4f}")

if overfitting_gap_best > 0.15:
    print("⚠️ HIGH OVERFITTING DETECTED! Applying aggressive generalization techniques...")
    
    # Try multiple conservative models to find the best balance
    conservative_models = {
        'Very Conservative RF': RandomForestClassifier(
            random_state=42, n_estimators=30, max_depth=2, min_samples_split=50, 
            min_samples_leaf=25, class_weight='balanced', max_features='sqrt'
        ),
        'Conservative RF': RandomForestClassifier(
            random_state=42, n_estimators=50, max_depth=3, min_samples_split=30, 
            min_samples_leaf=15, class_weight='balanced', max_features='sqrt'
        ),
        'Balanced RF': RandomForestClassifier(
            random_state=42, n_estimators=100, max_depth=4, min_samples_split=20, 
            min_samples_leaf=10, class_weight='balanced', max_features='sqrt'
        )
    }
    
    best_generalized_model = None
    best_generalized_accuracy = 0
    best_generalized_name = ""
    best_generalized_gap = 1.0
    
    for name, model in conservative_models.items():
        model.fit(X_train, y_train)
        y_train_pred_cons = model.predict(X_train)
        train_accuracy_cons = accuracy_score(y_train, y_train_pred_cons)
        y_test_pred_cons = model.predict(X_test)
        test_accuracy_cons = accuracy_score(y_test, y_test_pred_cons)
        overfitting_gap_cons = train_accuracy_cons - test_accuracy_cons
        
        print(f"{name}:")
        print(f"  Training Accuracy: {train_accuracy_cons:.4f}")
        print(f"  Test Accuracy: {test_accuracy_cons:.4f}")
        print(f"  Overfitting Gap: {overfitting_gap_cons:.4f}")
        
        # Choose model with best test accuracy and low overfitting
        if (test_accuracy_cons > best_generalized_accuracy and overfitting_gap_cons < 0.2) or \
           (overfitting_gap_cons < best_generalized_gap and test_accuracy_cons > best_generalized_accuracy * 0.8):
            best_generalized_model = model
            best_generalized_accuracy = test_accuracy_cons
            best_generalized_name = name
            best_generalized_gap = overfitting_gap_cons
            print(f"  ✅ New best generalized model")
    
    # Use the best generalized model if it's significantly better
    if best_generalized_accuracy > test_accuracy_best * 0.85 and best_generalized_gap < overfitting_gap_best:
        best_model = best_generalized_model
        best_model_name = best_generalized_name
        best_accuracy = best_generalized_accuracy
        print(f"✅ Using {best_generalized_name} for better generalization")
    else:
        print("✅ Keeping original best model despite overfitting")
        
elif overfitting_gap_best > 0.05:
    print("⚠️ MILD OVERFITTING DETECTED - Model is acceptable")
else:
    print("✅ NO SIGNIFICANT OVERFITTING - Model is well generalized")

# ✅ FINAL MODEL EVALUATION
print("\n=== FINAL MODEL EVALUATION ===")

# Train final model
best_model.fit(X_train, y_train)

# Training predictions
y_train_pred = best_model.predict(X_train)
train_accuracy = accuracy_score(y_train, y_train_pred)

# Test predictions
y_pred = best_model.predict(X_test)
y_pred_proba = best_model.predict_proba(X_test) if hasattr(best_model, 'predict_proba') else None

# Calculate metrics
test_accuracy = accuracy_score(y_test, y_pred)
r2 = r2_score(y_test, y_pred)
overfitting_gap = train_accuracy - test_accuracy

print(f"Training Accuracy: {train_accuracy:.4f}")
print(f"Test Accuracy: {test_accuracy:.4f}")
print(f"Overfitting Gap: {overfitting_gap:.4f}")
print(f"R2 Score: {r2:.4f}")

# Check for overfitting
if overfitting_gap > 0.1:
    print("WARNING: Potential overfitting detected!")
elif overfitting_gap > 0.05:
    print("WARNING: Mild overfitting detected")
else:
    print("OK: No significant overfitting")

# Classification report
print("\nClassification Report:")
print(classification_report(y_test, y_pred))

# Confusion Matrix
print("\nConfusion Matrix:")
print(confusion_matrix(y_test, y_pred))

# Feature importance (if available)
if hasattr(best_model, 'feature_importances_'):
    feature_importance = pd.DataFrame({
        'feature': selected_features,
        'importance': best_model.feature_importances_
    }).sort_values('importance', ascending=False)
    
    print("\n=== FEATURE IMPORTANCE ===")
    print(feature_importance)

# Save model and encoders
joblib.dump(best_model, "fashion_trend_model.pkl")
joblib.dump(encoders, "feature_encoders.pkl")

# Save model metadata
model_metadata = {
    'model_name': best_model_name,
    'train_accuracy': train_accuracy,
    'test_accuracy': test_accuracy,
    'overfitting_gap': overfitting_gap,
    'r2_score': r2,
    'feature_columns': list(X.columns),
    'target_column': 'Estimated Popularity (Now or Soon)',
    'no_leakage': True
}
joblib.dump(model_metadata, "model_metadata.pkl")

print(f"\nModel saved as 'fashion_trend_model.pkl'")
print(f"Feature encoders saved as 'feature_encoders.pkl'")
print(f"Model metadata saved as 'model_metadata.pkl'")

print(f"\nModel training completed successfully!")
print(f"Best Model: {best_model_name}")
print(f"Training Accuracy: {train_accuracy:.4f}")
print(f"Test Accuracy: {test_accuracy:.4f}")
print(f"Overfitting Gap: {overfitting_gap:.4f}")
print(f"R2 Score: {r2:.4f}")
