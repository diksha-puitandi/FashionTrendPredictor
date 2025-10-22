import requests

url = "http://127.0.0.1:5000/predict_trend"
data = {
    "Season & Weather Suitability :" : "Winter – Cold",
    "Target Audience :" : "Men",
    "Category & Fit :" : "Tops – Fitted",
    "Material / Fabric Type :" : "Cotton",
    "Cultural or Trend Influence :" : "K-pop",
    "Color & Pattern Type :" : "Beige",
    "Boldness & Emotional Impact :" : 10,
    "Was it Promoted by Celebrity or Influencer?" : "Yes",
    "Where Did You First See This?" : "TV / Magazine"
}

response = requests.post(url, json=data)
print(response.json())
