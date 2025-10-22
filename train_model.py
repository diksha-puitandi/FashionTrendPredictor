import pandas as pd
from sklearn.preprocessing import LabelEncoder
from sklearn.tree import DecisionTreeClassifier
from sklearn.metrics import accuracy_score
import joblib

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
processed_filename = "fashion_trends_prediction_corrected.csv"
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

# Train Decision Tree
print("\nTraining Decision Tree model...")
model = DecisionTreeClassifier(random_state=42)
model.fit(X_encoded, y)

# Predictions
y_pred = model.predict(X_encoded)
accuracy = accuracy_score(y, y_pred)
print(f"Training Accuracy: {accuracy:.2f}")

# Save model and encoders
joblib.dump(model, "fashion_trend_model.pkl")
joblib.dump(encoders, "feature_encoders.pkl")
print("Model saved as 'fashion_trend_model.pkl'")
print("Feature encoders saved as 'feature_encoders.pkl'")

# Display feature importance
feature_importance = pd.DataFrame({
    'feature': X.columns,
    'importance': model.feature_importances_
}).sort_values('importance', ascending=False)

print("\n=== FEATURE IMPORTANCE ===")
print(feature_importance)

print("\nFeature engineering and model training completed!")
