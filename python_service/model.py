import torch
import torch.nn as nn
import cv2
import numpy as np
from torchvision import transforms
from PIL import Image
from pathlib import Path
import os

class EmotionNet(nn.Module):
    """
    EmotionNet architecture - EXACT match with trained model
    Based on actual .pth layer structure
    """
    def __init__(self, num_classes=7):
        super(EmotionNet, self).__init__()
        
        # Layer 1: Conv + BN + ReLU + Pool + Dropout
        self.conv1 = nn.Conv2d(1, 32, kernel_size=3, padding=1)
        self.bn1 = nn.BatchNorm2d(32)
        self.relu1 = nn.ReLU(inplace=True)
        self.pool1 = nn.MaxPool2d(kernel_size=2, stride=2)
        self.dropout1 = nn.Dropout(0.25)
        
        # Layer 2: Conv + BN + ReLU + Pool + Dropout
        self.conv2 = nn.Conv2d(32, 64, kernel_size=3, padding=1)
        self.bn2 = nn.BatchNorm2d(64)
        self.relu2 = nn.ReLU(inplace=True)
        self.pool2 = nn.MaxPool2d(kernel_size=2, stride=2)
        self.dropout2 = nn.Dropout(0.25)
        
        # Layer 3: Conv + BN + ReLU + Pool + Dropout
        self.conv3 = nn.Conv2d(64, 128, kernel_size=3, padding=1)
        self.bn3 = nn.BatchNorm2d(128)
        self.relu3 = nn.ReLU(inplace=True)
        self.pool3 = nn.MaxPool2d(kernel_size=2, stride=2)
        self.dropout3 = nn.Dropout(0.25)
        
        # Layer 4: Conv + BN + ReLU + Pool + Dropout
        self.conv4 = nn.Conv2d(128, 128, kernel_size=3, padding=1)
        self.bn4 = nn.BatchNorm2d(128)
        self.relu4 = nn.ReLU(inplace=True)
        self.pool4 = nn.MaxPool2d(kernel_size=2, stride=2)
        self.dropout4 = nn.Dropout(0.25)
        
        # Layer 5: Conv + BN + ReLU + NO POOL! + Dropout
        self.conv5 = nn.Conv2d(128, 256, kernel_size=3, padding=1)
        self.bn5 = nn.BatchNorm2d(256)
        self.relu5 = nn.ReLU(inplace=True)
        # NO POOLING HERE! Feature map stays 3x3
        self.dropout5 = nn.Dropout(0.25)
        
        # Fully connected layers
        # After 4 pools (no pool on conv5): 48 -> 24 -> 12 -> 6 -> 3
        # Final feature map: 256 * 3 * 3 = 2304
        self.fc1 = nn.Linear(256 * 3 * 3, 1024)  # 2304 -> 1024
        self.bn_fc1 = nn.BatchNorm1d(1024)
        self.relufc = nn.ReLU(inplace=True)
        self.dropoutfc = nn.Dropout(0.5)
        self.fc2 = nn.Linear(1024, 7)  # 1024 -> 7
    
    def forward(self, x):
        # Conv block 1
        x = self.conv1(x)
        x = self.bn1(x)
        x = self.relu1(x)
        x = self.pool1(x)
        x = self.dropout1(x)
        
        # Conv block 2
        x = self.conv2(x)
        x = self.bn2(x)
        x = self.relu2(x)
        x = self.pool2(x)
        x = self.dropout2(x)
        
        # Conv block 3
        x = self.conv3(x)
        x = self.bn3(x)
        x = self.relu3(x)
        x = self.pool3(x)
        x = self.dropout3(x)
        
        # Conv block 4
        x = self.conv4(x)
        x = self.bn4(x)
        x = self.relu4(x)
        x = self.pool4(x)
        x = self.dropout4(x)
        
        # Conv block 5 (NO POOLING!)
        x = self.conv5(x)
        x = self.bn5(x)
        x = self.relu5(x)
        # NO POOLING - feature map stays 3x3
        x = self.dropout5(x)
        
        # Flatten
        x = x.view(x.size(0), -1)
        
        # FC layers
        x = self.fc1(x)
        x = self.bn_fc1(x)
        x = self.relufc(x)
        x = self.dropoutfc(x)
        x = self.fc2(x)
        
        return x


class EmotionDetector:
    """Emotion detection model handler - matches inference_example.py preprocessing"""
    
    def __init__(self, model_path: str):
        self.device = torch.device('cuda' if torch.cuda.is_available() else 'cpu')
        self.model_path = model_path
        self.model = None
        
        # CRITICAL: Must match training order (from inference_example.py)
        self.emotions = ['angry', 'disgust', 'fear', 'happy', 'neutral', 'sad', 'surprise']
        
        # Face detection using Haar Cascade
        cascade_path = cv2.data.haarcascades + 'haarcascade_frontalface_default.xml'
        self.face_cascade = cv2.CascadeClassifier(cascade_path)
        
        self.load_model()
    
    def load_model(self):
        """Load PyTorch model from .pth file"""
        try:
            # Create model instance
            self.model = EmotionNet(num_classes=len(self.emotions))
            
            # Load weights - EXACTLY like inference_example.py
            checkpoint = torch.load(self.model_path, map_location=self.device, weights_only=False)
            
            # Handle different checkpoint formats
            if isinstance(checkpoint, dict):
                if 'model_state_dict' in checkpoint:
                    state_dict = checkpoint['model_state_dict']
                elif 'state_dict' in checkpoint:
                    state_dict = checkpoint['state_dict']
                else:
                    # Assume the dict itself is the state_dict
                    state_dict = checkpoint
            else:
                # Direct state_dict
                state_dict = checkpoint
            
            # Load state dict
            self.model.load_state_dict(state_dict)
            self.model.to(self.device)
            self.model.eval()  # CRITICAL: Set to eval mode
            
            print(f"Model loaded successfully from {self.model_path}")
            print(f"   Device: {self.device}")
            print(f"   Emotions: {self.emotions}")
            
        except Exception as e:
            print(f"ERROR loading model: {str(e)}")
            import traceback
            traceback.print_exc()
            raise
    
    def detect_face(self, image: np.ndarray):
        """Detect face in image using OpenCV"""
        # Convert to grayscale for face detection
        if len(image.shape) == 3:
            gray = cv2.cvtColor(image, cv2.COLOR_BGR2GRAY)
        else:
            gray = image
            
        # Detect faces - RELAXED parameters for better detection
        faces = self.face_cascade.detectMultiScale(
            gray,
            scaleFactor=1.05,  # Lower = more sensitive (was 1.1)
            minNeighbors=3,    # Lower = more sensitive (was 5)
            minSize=(30, 30)   # Smaller minimum (was 48x48)
        )
        
        if len(faces) == 0:
            return None, None
        
        # Get the largest face
        faces_sorted = sorted(faces, key=lambda x: x[2] * x[3], reverse=True)
        x, y, w, h = faces_sorted[0]
        
        face_roi = gray[y:y+h, x:x+w]
        return face_roi, (x, y, w, h)
    
    def preprocess_face_for_emotion(self, face_image: np.ndarray):
        """
        CRITICAL: Exact preprocessing pipeline from inference_example.py
        
        Args:
            face_image: numpy array (grayscale)
        
        Returns:
            tensor: preprocessed tensor [1, 1, 48, 48]
        """
        # 1. Ensure grayscale
        if len(face_image.shape) == 3:
            gray = cv2.cvtColor(face_image, cv2.COLOR_BGR2GRAY)
        else:
            gray = face_image
        
        # 2. Resize to 48x48 (IMPORTANT: use INTER_AREA like in inference_example.py)
        resized = cv2.resize(gray, (48, 48), interpolation=cv2.INTER_AREA)
        
        # 3. Convert to tensor and normalize
        # CRITICAL: Same normalization as training (mean=0.5, std=0.5)
        transform = transforms.Compose([
            transforms.ToTensor(),  # Converts to [0, 1] and adds channel dimension
            transforms.Normalize(mean=[0.5], std=[0.5])  # CRITICAL: Must match training
        ])
        
        tensor = transform(resized)
        
        # 4. Add batch dimension
        tensor = tensor.unsqueeze(0)  # Shape: [1, 1, 48, 48]
        tensor = tensor.to(self.device)
        
        return tensor
    
    def predict_emotion(self, image_bytes: bytes):
        """
        Predict emotion from image bytes
        Returns: dict with emotion, confidence, and face bounding box
        """
        try:
            # Convert bytes to numpy array
            nparr = np.frombuffer(image_bytes, np.uint8)
            image = cv2.imdecode(nparr, cv2.IMREAD_COLOR)
            
            if image is None:
                return {
                    'success': False,
                    'error': 'Invalid image format'
                }
            
            # Try to detect face first
            face_roi, bbox = self.detect_face(image)
            
            # If no face detected, use center crop as fallback
            if face_roi is None:
                print("WARNING: No face detected, using center crop")
                h, w = image.shape[:2]
                
                # Take center 60% of image
                crop_h, crop_w = int(h * 0.6), int(w * 0.6)
                start_y = (h - crop_h) // 2
                start_x = (w - crop_w) // 2
                
                # Convert to grayscale and crop
                gray = cv2.cvtColor(image, cv2.COLOR_BGR2GRAY)
                face_roi = gray[start_y:start_y+crop_h, start_x:start_x+crop_w]
                bbox = (start_x, start_y, crop_w, crop_h)
                
                print(f"   Using center crop: {crop_w}x{crop_h}")
            
            # Preprocess face - using EXACT pipeline from inference_example.py
            input_tensor = self.preprocess_face_for_emotion(face_roi)
            
            # Predict
            self.model.eval()  # Ensure eval mode
            with torch.no_grad():  # Disable gradients
                outputs = self.model(input_tensor)
                probabilities = torch.nn.functional.softmax(outputs, dim=1)
                confidence, predicted = torch.max(probabilities, 1)
                
                emotion_idx = predicted.item()
                emotion = self.emotions[emotion_idx]
                conf_score = confidence.item()
            
            print(f"Prediction: {emotion} ({conf_score*100:.1f}%)")
            
            return {
                'success': True,
                'emotion': emotion,
                'confidence': float(conf_score),
                'all_probabilities': {
                    self.emotions[i]: float(probabilities[0][i].item())
                    for i in range(len(self.emotions))
                },
                'face_bbox': {
                    'x': int(bbox[0]),
                    'y': int(bbox[1]),
                    'width': int(bbox[2]),
                    'height': int(bbox[3])
                }
            }
            
        except Exception as e:
            import traceback
            error_trace = traceback.format_exc()
            print(f"Prediction error: {str(e)}")
            print(error_trace)
            return {
                'success': False,
                'error': f'Prediction error: {str(e)}',
                'traceback': error_trace
            }
