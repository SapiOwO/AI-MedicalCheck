import requests
import cv2
import numpy as np
from PIL import Image
import io

print("=" * 60)
print("Creating Test Image with Clear Face Pattern")
print("=" * 60)

# Create a more realistic test - a simple face-like pattern
img = np.zeros((200, 200, 3), dtype=np.uint8)

# Background
img[:] = (200, 200, 200)

# Face oval (skin tone)
cv2.ellipse(img, (100, 100), (60, 80), 0, 0, 360, (220, 180, 140), -1)

# Eyes
cv2.circle(img, (80, 85), 8, (0, 0, 0), -1)
cv2.circle(img, (120, 85), 8, (0, 0, 0), -1)

# Nose
cv2.line(img, (100, 90), (100, 110), (180, 140, 100), 2)

# Mouth (smile)
cv2.ellipse(img, (100, 120), (20, 10), 0, 0, 180, (180, 0, 0), 2)

cv2.imwrite('test_face_realistic.jpg', img)
print("✅ Test image created: test_face_realistic.jpg")

# Test with API
print("\n" + "=" * 60)
print("Sending to Python API")
print("=" * 60)

try:
    with open('test_face_realistic.jpg', 'rb') as f:
        files = {'file': ('test.jpg', f, 'image/jpeg')}
        response = requests.post('http://localhost:8001/predict', files=files)
    
    print(f"Status Code: {response.status_code}")
    print(f"Response:")
    print(response.text)
    
except Exception as e:
    print(f"❌ Error: {e}")
    import traceback
    traceback.print_exc()
