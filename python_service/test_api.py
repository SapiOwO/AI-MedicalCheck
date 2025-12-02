import requests
import json

# Test Python API directly
url = "http://localhost:8001/health"

print("=" * 60)
print("Testing Python API Health Check")
print("=" * 60)

try:
    response = requests.get(url)
    print(f"Status Code: {response.status_code}")
    print(f"Response: {json.dumps(response.json(), indent=2)}")
except Exception as e:
    print(f"Error: {e}")

print("\n" + "=" * 60)
print("Testing Python API Prediction with Test Image")
print("=" * 60)

# Test with a simple image
import cv2
import numpy as np

# Create a test image (gray square)
test_img = np.ones((48, 48, 3), dtype=np.uint8) * 128
cv2.imwrite('test_face.jpg', test_img)

# Send to API
try:
    with open('test_face.jpg', 'rb') as f:
        files = {'file': ('test_face.jpg', f, 'image/jpeg')}
        response = requests.post('http://localhost:8001/predict', files=files)
    
    print(f"Status Code: {response.status_code}")
    print(f"Response: {json.dumps(response.json(), indent=2)}")
except Exception as e:
    print(f"Error: {e}")
