import pandas as pd
import numpy as np
from sklearn.preprocessing import LabelEncoder
from sklearn.model_selection import train_test_split, GridSearchCV, cross_val_score
from sklearn.tree import DecisionTreeClassifier
from sklearn.ensemble import RandomForestClassifier, GradientBoostingClassifier
from sklearn.linear_model import LogisticRegression
from sklearn.svm import SVC
from sklearn.metrics import accuracy_score, classification_report, confusion_matrix, r2_score
from sklearn.preprocessing import StandardScaler
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
print("\n=== TRAINING MODEL ===")

# Drop irrelevant columns (keep the new engineered features)
columns_to_drop = ["Timestamp", "Full Name :", "Email Id :"]
data_clean = data.drop(columns_to_drop, axis=1)

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

# ✅ MODEL OPTIMIZATION - Test multiple algorithms
print("\n=== MODEL OPTIMIZATION ===")

models = {
    'Decision Tree': DecisionTreeClassifier(random_state=42),
    'Random Forest': RandomForestClassifier(random_state=42, n_estimators=100),
    'Gradient Boosting': GradientBoostingClassifier(random_state=42),
    'Logistic Regression': LogisticRegression(random_state=42, max_iter=1000),
    'SVM': SVC(random_state=42)
}

best_model = None
best_accuracy = 0
best_model_name = ""

print("Testing different algorithms...")
for name, model in models.items():
    # Train model
    model.fit(X_train, y_train)
    
    # Test model
    y_pred = model.predict(X_test)
    accuracy = accuracy_score(y_test, y_pred)
    
    # Cross-validation
    cv_scores = cross_val_score(model, X_train, y_train, cv=5)
    
    print(f"{name}:")
    print(f"  Test Accuracy: {accuracy:.4f}")
    print(f"  CV Mean: {cv_scores.mean():.4f} (+/- {cv_scores.std() * 2:.4f})")
    
    if accuracy > best_accuracy:
        best_accuracy = accuracy
        best_model = model
        best_model_name = name

print(f"\nBest model: {best_model_name} with accuracy: {best_accuracy:.4f}")

# ✅ HYPERPARAMETER TUNING for best model
print(f"\n=== HYPERPARAMETER TUNING for {best_model_name} ===")

if best_model_name == 'Random Forest':
    param_grid = {
        'n_estimators': [50, 100, 200],
        'max_depth': [None, 10, 20, 30],
        'min_samples_split': [2, 5, 10],
        'min_samples_leaf': [1, 2, 4]
    }
elif best_model_name == 'Gradient Boosting':
    param_grid = {
        'n_estimators': [50, 100, 200],
        'learning_rate': [0.01, 0.1, 0.2],
        'max_depth': [3, 5, 7],
        'subsample': [0.8, 0.9, 1.0]
    }
elif best_model_name == 'Decision Tree':
    param_grid = {
        'max_depth': [None, 5, 10, 15, 20],
        'min_samples_split': [2, 5, 10, 20],
        'min_samples_leaf': [1, 2, 4, 8],
        'criterion': ['gini', 'entropy']
    }
else:
    param_grid = {}

if param_grid:
    print("Performing Grid Search...")
    grid_search = GridSearchCV(
        best_model, param_grid, cv=5, scoring='accuracy', n_jobs=-1
    )
    grid_search.fit(X_train, y_train)
    
    best_model = grid_search.best_estimator_
    print(f"Best parameters: {grid_search.best_params_}")
    print(f"Best CV score: {grid_search.best_score_:.4f}")

# ✅ FINAL MODEL EVALUATION
print("\n=== FINAL MODEL EVALUATION ===")

# Train final model
best_model.fit(X_train, y_train)

# Test predictions
y_pred = best_model.predict(X_test)
y_pred_proba = best_model.predict_proba(X_test) if hasattr(best_model, 'predict_proba') else None

# Calculate metrics
test_accuracy = accuracy_score(y_test, y_pred)
r2 = r2_score(y_test, y_pred)

print(f"Final Test Accuracy: {test_accuracy:.4f}")
print(f"R2 Score: {r2:.4f}")

# Classification report
print("\nClassification Report:")
print(classification_report(y_test, y_pred))

# Confusion Matrix
print("\nConfusion Matrix:")
print(confusion_matrix(y_test, y_pred))

# Feature importance (if available)
if hasattr(best_model, 'feature_importances_'):
    feature_importance = pd.DataFrame({
        'feature': X.columns,
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
    'test_accuracy': test_accuracy,
    'r2_score': r2,
    'feature_columns': list(X.columns),
    'target_column': 'Estimated Popularity (Now or Soon)'
}
joblib.dump(model_metadata, "model_metadata.pkl")

print(f"\nModel saved as 'fashion_trend_model.pkl'")
print(f"Feature encoders saved as 'feature_encoders.pkl'")
print(f"Model metadata saved as 'model_metadata.pkl'")

print(f"\nModel training completed successfully!")
print(f"Best Model: {best_model_name}")
print(f"Test Accuracy: {test_accuracy:.4f}")
print(f"R2 Score: {r2:.4f}")
