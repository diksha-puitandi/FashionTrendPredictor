import joblib

# Load the model metadata
metadata = joblib.load('automl_model_metadata.pkl')

print("Model Training Accuracy Analysis:")
print("=" * 40)
print(f"Train Accuracy: {metadata.get('train_accuracy', 0):.4f} ({metadata.get('train_accuracy', 0)*100:.1f}%)")
print(f"Test Accuracy: {metadata.get('test_accuracy', 0):.4f} ({metadata.get('test_accuracy', 0)*100:.1f}%)")
print(f"R2 Score: {metadata.get('r2_score', 0):.4f}")
print(f"R2 Score Normalized: {metadata.get('r2_score_normalized', 0):.4f}")
print(f"Model Name: {metadata.get('model_name', 'Unknown')}")

# Calculate what the display accuracy would be
original_train_accuracy = metadata.get('train_accuracy', 0)
original_test_accuracy = metadata.get('test_accuracy', 0)

# Apply the same calculation as in predict_trend.py
base_train_accuracy = max(0.2, original_train_accuracy * 0.3)
display_train_accuracy = min(1.0, base_train_accuracy + 0.6)

base_test_accuracy = max(0.2, original_test_accuracy * 0.3)
display_test_accuracy = min(1.0, base_test_accuracy + 0.6)

print(f"\nAccuracy Calculations:")
print(f"  Original Train Accuracy: {original_train_accuracy:.4f} ({original_train_accuracy*100:.1f}%)")
print(f"  Display Train Accuracy: {display_train_accuracy:.4f} ({display_train_accuracy*100:.1f}%)")
print(f"  Original Test Accuracy: {original_test_accuracy:.4f} ({original_test_accuracy*100:.1f}%)")
print(f"  Display Test Accuracy: {display_test_accuracy:.4f} ({display_test_accuracy*100:.1f}%)")

print(f"\nOverfitting Analysis:")
overfitting_gap = metadata.get('overfitting_gap', 0)
print(f"  Overfitting Gap: {overfitting_gap:.4f}")
if overfitting_gap > 0.1:
    print(f"  Status: Potential overfitting detected")
elif overfitting_gap < 0.05:
    print(f"  Status: Good generalization")
else:
    print(f"  Status: Moderate overfitting")
