import sys
import json
import pandas as pd
import joblib
import numpy as np
from sklearn.preprocessing import LabelEncoder

def predict_trend(input_data):
    try:
        # Load the trained model and encoders
        model = joblib.load("fashion_trend_model.pkl")
        encoders = joblib.load("feature_encoders.pkl")
        metadata = joblib.load("model_metadata.pkl")
        
        # Create a DataFrame with only the selected features (from feature selection)
        # Selected features: ['Age :', 'Category & Fit :', 'Cultural or Trend Influence :', 
        # 'Color & Pattern Type :', 'Boldness & Emotional Impact :', 
        # 'Was it Promoted by Celebrity or Influencer?', 'Where Did You First See This?']
        data = {
            'Age :': input_data['age'],
            'Category & Fit :': input_data['category_fit'],
            'Cultural or Trend Influence :': input_data['cultural_trend'],
            'Color & Pattern Type :': input_data['color_pattern'],
            'Boldness & Emotional Impact :': input_data['boldness'],
            'Was it Promoted by Celebrity or Influencer?': input_data['celebrity_promotion'],
            'Where Did You First See This?': input_data['first_seen']
        }
        
        # Create DataFrame
        df = pd.DataFrame([data])
        
        # Map input values to match training data format (only for selected features)
        # Age mapping
        age_mapping = {
            'under_18': 'Under 18',
            '18_to_25': '18 to 25',
            '25_to_30': '25 to 30',
            '31_and_above': '31 and above'
        }
        df['Age :'] = age_mapping.get(input_data['age'], input_data['age'])
        
        # Category & Fit mapping
        category_mapping = {
            'tops_and_loose': 'Tops and loose',
            'tops_and_fitted': 'Tops and fitted',
            'bottoms_and_flare': 'Bottoms and flare',
            'bottoms_and_straight': 'Bottoms and straight',
            'dresses_and_bodycon': 'Dresses and bodycon',
            'dresses_and_aline': 'Dresses and A-line',
            'others': 'Others'
        }
        df['Category & Fit :'] = category_mapping.get(input_data['category_fit'], input_data['category_fit'])
        
        # Cultural trend mapping
        cultural_mapping = {
            'kpop': 'K-pop',
            'y2k': 'Y2K',
            'vintage': 'Vintage',
            'boho': 'Boho',
            'streetwear': 'Streetwear',
            'minimalist': 'Minimalist',
            'other': 'Other'
        }
        df['Cultural or Trend Influence :'] = cultural_mapping.get(input_data['cultural_trend'], input_data['cultural_trend'])
        
        # Celebrity promotion mapping
        celebrity_mapping = {
            'yes': 'Yes',
            'no': 'No',
            'not_sure': 'Not sure'
        }
        df['Was it Promoted by Celebrity or Influencer?'] = celebrity_mapping.get(input_data['celebrity_promotion'], input_data['celebrity_promotion'])
        
        # First seen mapping
        first_seen_mapping = {
            'instagram_tiktok': 'Instagram/TikTok',
            'fashion_show': 'Fashion show',
            'online_store': 'Online store',
            'street_style': 'Street style',
            'tv_magazine': 'TV/Magazine',
            'other': 'Other'
        }
        df['Where Did You First See This?'] = first_seen_mapping.get(input_data['first_seen'], input_data['first_seen'])
        
        # Note: We no longer add engineered features to avoid data leakage
        # The model now works with only the original input features
        
        # Encode categorical features using the same encoders from training
        df_encoded = df.copy()
        for col in df.columns:
            if col in encoders:
                le = encoders[col]
                # Handle unseen categories
                try:
                    df_encoded[col] = le.transform(df[col].astype(str))
                except ValueError:
                    # If category not seen during training, use the most frequent class
                    df_encoded[col] = 0
        
        # Make prediction
        prediction = model.predict(df_encoded)[0]
        prediction_proba = model.predict_proba(df_encoded)[0] if hasattr(model, 'predict_proba') else None
        
        # Get model metadata
        model_name = metadata.get('model_name', 'Unknown')
        train_accuracy = metadata.get('train_accuracy', 0.0)
        test_accuracy = metadata.get('test_accuracy', 0.0)
        overfitting_gap = metadata.get('overfitting_gap', 0.0)
        r2_score = metadata.get('r2_score', 0.0)
        no_leakage = metadata.get('no_leakage', False)
        
        # Create trend series for visualization
        trend_series = {
            "labels": ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
            "values": [50, 55, 60, 65, 70, 75, 80, 85, 90, 85, 80, 75]
        }
        
        # Adjust trend based on prediction
        base_trend = [50, 55, 60, 65, 70, 75, 80, 85, 90, 85, 80, 75]
        prediction_multiplier = prediction / 3.0  # Normalize to 0-1.67 range
        trend_series["values"] = [int(val * prediction_multiplier) for val in base_trend]
        
        # Create category and estimated years based on prediction
        category_mapping = {1: "Very Low", 2: "Low", 3: "Medium", 4: "High", 5: "Very High"}
        years_mapping = {1: "0-1", 2: "2-3", 3: "5", 4: "6-7", 5: "9-10"}
        
        category = category_mapping.get(prediction, "Unknown")
        estimated_years = years_mapping.get(prediction, "Unknown")
        
        result = {
            "success": True,
            "prediction": int(prediction),
            "prediction_text": f"Estimated Popularity: {prediction}/5",
            "category": category,
            "estimated_years": estimated_years,
            "model_info": {
                "model_name": model_name,
                "train_accuracy": round(train_accuracy, 4),
                "test_accuracy": round(test_accuracy, 4),
                "overfitting_gap": round(overfitting_gap, 4),
                "r2_score": round(r2_score, 4),
                "no_leakage": no_leakage
            },
            "trend_series": trend_series,
            "confidence": float(max(prediction_proba)) if prediction_proba is not None else 0.0
        }
        
        return result
        
    except Exception as e:
        return {
            "success": False,
            "error": f"Prediction failed: {str(e)}"
        }

if __name__ == "__main__":
    try:
        # Get input data from command line argument
        if len(sys.argv) < 2:
            error_result = {
                "success": False,
                "error": "No input data provided"
            }
            print(json.dumps(error_result))
            sys.exit(1)
            
        input_json = sys.argv[1]
        # Clean the input JSON string
        input_json = input_json.strip().strip("'").strip('"')
        input_data = json.loads(input_json)
        
        # Make prediction
        result = predict_trend(input_data)
        
        # Output result as JSON
        print(json.dumps(result))
        
    except json.JSONDecodeError as e:
        error_result = {
            "success": False,
            "error": f"JSON parsing failed: {str(e)}"
        }
        print(json.dumps(error_result))
    except Exception as e:
        error_result = {
            "success": False,
            "error": f"Script execution failed: {str(e)}"
        }
        print(json.dumps(error_result))
