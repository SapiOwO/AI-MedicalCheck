import torch
from pathlib import Path

# Load model checkpoint
model_path = Path("../AImodel/emotion_best.pth")
checkpoint = torch.load(model_path, map_location='cpu')

print("=" * 60)
print("MODEL CHECKPOINT INSPECTION")
print("=" * 60)

# Check structure
if isinstance(checkpoint, dict):
    print("\nCheckpoint keys:")
    for key in checkpoint.keys():
        print(f"  - {key}")
    
    # Get state_dict
    if 'model_state_dict' in checkpoint:
        state_dict = checkpoint['model_state_dict']
    elif 'state_dict' in checkpoint:
        state_dict = checkpoint['state_dict']
    else:
        state_dict = checkpoint
else:
    state_dict = checkpoint

print("\n" + "=" * 60)
print("MODEL LAYERS:")
print("=" * 60)

# Print all layer names
for idx, (name, param) in enumerate(state_dict.items(), 1):
    print(f"{idx:3d}. {name:40s} {list(param.shape)}")

print("\n" + "=" * 60)
print(f"Total layers: {len(state_dict)}")
print("=" * 60)
