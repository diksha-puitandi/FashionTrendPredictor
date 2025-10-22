# from flask import Flask
# import joblib
# import os

# app = Flask(__name__)

# MODEL_PATH = os.path.join(os.path.dirname(__file__), "model_4_features.pkl")
# model = joblib.load(MODEL_PATH)

# @app.route('/predict', methods=['GET'])
# def predict():
#     # Example: use a default input instead of reading from request
#     input_data = [[5.1, 3.5, 1.4, 0.2]]  # Replace with your model's expected input
#     prediction = model.predict(input_data)
#     return f"Prediction: {prediction[0]}"

# if __name__ == "__main__":
#     app.run(debug=True)


# import pandas as pd
# from flask import Flask, request, jsonify
# import joblib
# from sklearn.ensemble import RandomForestClassifier
# from sklearn.model_selection import train_test_split

# app = Flask(__name__)

# # Load your trained 4-feature model
# MODEL_PATH = "ftp_model.pkl"
# model = joblib.load(MODEL_PATH)

# @app.route('/predict', methods=['GET', 'POST'])
# def predict():
#     if request.method == 'POST':
#         data = request.get_json()
#         input_data = pd.DataFrame([data], columns=model.feature_names_in_)
#         prediction = model.predict(input_data)
#         return jsonify({'prediction': int(prediction[0])})
#     else:
#         return "Send a POST request with JSON data to get a prediction."


# if __name__ == '__main__':
#     app.run(debug=True)





# from flask import Flask, request, jsonify
# import joblib
# import pandas as pd

# app = Flask(__name__)

# # Load the model
# model = joblib.load("ftp_model.pkl")

# # Load original dataset to recreate encoders
# file_path = "fashion_trends_prediction.csv"
# data = pd.read_csv(file_path)

# # Drop unnecessary columns
# X = data.drop(["Timestamp", "Full Name : ", "Email Id : ", "Estimated Popularity (Now or Soon) : "], axis=1)

# # Fit LabelEncoders for each categorical column
# from sklearn.preprocessing import LabelEncoder

# encoders = {}
# for col in X.columns:
#     if X[col].dtype == "object":
#         le = LabelEncoder()
#         X[col] = le.fit_transform(X[col].astype(str))
#         encoders[col] = le

# @app.route('/')
# def home():
#     return "✅ Fashion Trend Prediction API is running! Send a POST request to /predict with JSON data."

# @app.route('/predict', methods=['POST'])
# def predict_api():
#     try:
#         # Get JSON data
#         data = request.get_json()

#         # Convert into DataFrame
#         df = pd.DataFrame([data])

#         # Apply same encoding as training
#         for col in df.columns:
#             if col in encoders:
#                 df[col] = encoders[col].transform(df[col].astype(str))

#         # Prediction
#         prediction = model.predict(df)[0]

#         return jsonify({"prediction": int(prediction)})

#     except Exception as e:
#         return jsonify({"error": str(e)})

# if __name__ == "__main__":
#     app.run(debug=True)


import os
import pandas as pd
import joblib
from flask import Flask, request, jsonify
from flask_cors import CORS

# ----------------------
# Initialize Flask app
# ----------------------
app = Flask(__name__)
CORS(app)

# ----------------------
# Load trained model
# ----------------------
MODEL_PATH = os.path.join(os.path.dirname(__file__), 'ftp_model.pkl')  # adjust if needed

if os.path.exists(MODEL_PATH):
    try:
        model = joblib.load(MODEL_PATH)
        print("Model loaded successfully.")
    except Exception as e:
        print(f"Error loading model: {e}")
        model = None
else:
    print(f"Error: Model file not found at {MODEL_PATH}")
    model = None

# ----------------------
# Feature order as in CSV
# ----------------------
FEATURE_ORDER = [
    'Age :',
    'Season & Weather Suitability :',
    'Target Audience :',
    'Category & Fit :',
    'Material / Fabric Type :',
    'Cultural or Trend Influence :',
    'Color & Pattern Type :',
    'Boldness & Emotional Impact :',
    'Was it Promoted by Celebrity or Influencer?',
    'Where Did You First See This?'
]

# ----------------------
# Predict endpoint
# ----------------------
@app.route('/predict_trend', methods=['POST'])
def predict_trend():
    if model is None:
        return jsonify({"error": "Model not loaded. Cannot make predictions."}), 500

    try:
        data = request.get_json()

        # ----------------------
        # Map frontend input exactly as CSV column names
        # ----------------------
        model_input = {}
        for feature in FEATURE_ORDER:
            if feature == 'Boldness & Emotional Impact :':
                model_input[feature] = pd.to_numeric(data.get(feature, 0), errors='coerce')
            else:
                model_input[feature] = data.get(feature, '')

        # ----------------------
        # Convert to DataFrame with exact CSV column order
        # ----------------------
        df_input = pd.DataFrame([model_input], columns=FEATURE_ORDER)

        # ----------------------
        # Make prediction
        # ----------------------
        prediction = model.predict(df_input)
        trending = int(prediction[0])

        # ----------------------
        # Return JSON response
        # ----------------------
        return jsonify({
            "trending": trending,
            "trend_series": {
                "labels": ["Jan", "Feb", "Mar", "Apr", "May", "Jun"],
                "values": [40, 55, 70, 65, 80, 90]  # dummy trend series
            }
        })

    except Exception as e:
        print(f"Prediction error: {e}")
        return jsonify({"error": str(e)}), 500

# ----------------------
# Run Flask app
# ----------------------
if __name__ == '__main__':
    app.run(debug=True)


