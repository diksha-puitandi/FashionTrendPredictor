from predict_trend import predict_trend

# Test cases
test_cases = [
    {
        'age': '18_to_25',
        'season_weather': 'summer_hot',
        'target_audience': 'women',
        'category_fit': 'formal',
        'material_fabric': 'silk',
        'cultural_trend': 'minimalist',
        'color_pattern': 'patterned',
        'boldness': 1,
        'celebrity_promotion': 'yes',
        'first_seen': 'fashion_show'
    },
    {
        'age': '31_and_above',
        'season_weather': 'winter_cold',
        'target_audience': 'men',
        'category_fit': 'streetwear',
        'material_fabric': 'denim',
        'cultural_trend': 'streetwear',
        'color_pattern': 'solid',
        'boldness': 5,
        'celebrity_promotion': 'no',
        'first_seen': 'social_media'
    },
    {
        'age': '26_to_30',
        'season_weather': 'spring_mild',
        'target_audience': 'women',
        'category_fit': 'casual',
        'material_fabric': 'cotton',
        'cultural_trend': 'bohemian',
        'color_pattern': 'floral',
        'boldness': 3,
        'celebrity_promotion': 'no',
        'first_seen': 'online_store'
    }
]

print("Testing AutoML Model Performance:")
print("=" * 50)

for i, case in enumerate(test_cases, 1):
    result = predict_trend(case)
    if result['success']:
        print(f"Test {i}:")
        print(f"  Prediction: {result['prediction']}/5")
        print(f"  Confidence: {result['confidence']:.3f}")
        print(f"  R2 Score: {result['model_info']['r2_score']}")
        print(f"  Model: {result['model_info']['model_name']}")
        print()
    else:
        print(f"Test {i}: Failed - {result['error']}")
        print()
