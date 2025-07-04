import cv2
import numpy as np
import sys
import os
from design_ai_helper import suggest_design_improvements
sys.stdout.reconfigure(encoding='utf-8')

# --- Configuration Parameters ---
MIN_LOGO_DIMENSION_PX = 300
MIN_CONTOUR_AREA_PX = 50
SHARPNESS_THRESHOLD = 100
CANNY_THRESHOLD1 = 50
CANNY_THRESHOLD2 = 150

# --- Read file path ---
if len(sys.argv) < 2:
    print("❌ Usage: python analyze_logo.py <path/to/image>")
    sys.exit(1)

file_path = sys.argv[1]

# Normalize and verify file path
file_path = os.path.abspath(file_path)
if not os.path.isfile(file_path):
    print(f"❌ File not found: {file_path}")
    sys.exit(1)

# Load image
image = cv2.imread(file_path, cv2.IMREAD_UNCHANGED)
if image is None:
    print(f"❌ Could not load image: {file_path}")
    sys.exit(1)

errors = []
warnings = []

# Convert to grayscale
if len(image.shape) == 3:
    gray = cv2.cvtColor(image, cv2.COLOR_BGR2GRAY)
else:
    gray = image

# Edge detection
edges = cv2.Canny(gray, CANNY_THRESHOLD1, CANNY_THRESHOLD2)

# Find contours
contours_info = cv2.findContours(edges, cv2.RETR_EXTERNAL, cv2.CHAIN_APPROX_SIMPLE)
if len(contours_info) == 3:
    _, contours, _ = contours_info
else:
    contours, _ = contours_info

# Filter small contours
significant_contours = [c for c in contours if cv2.contourArea(c) > MIN_CONTOUR_AREA_PX]

# --- Checks ---
# 1. Resolution
if image.shape[0] < MIN_LOGO_DIMENSION_PX or image.shape[1] < MIN_LOGO_DIMENSION_PX:
    errors.append(f"📏 Resolution too low: {image.shape[1]}x{image.shape[0]} px (min {MIN_LOGO_DIMENSION_PX}px).")

# 2. Sharpness
laplacian_var = cv2.Laplacian(gray, cv2.CV_64F).var()
if laplacian_var < SHARPNESS_THRESHOLD:
    warnings.append(f"🔧 Image may be blurry (Sharpness score: {laplacian_var:.2f}).")

# 3. Alpha channel presence
has_alpha = len(image.shape) == 3 and image.shape[2] == 4
if not has_alpha:
    warnings.append("⚠️ No transparency (alpha) channel detected.")
else:
    alpha_channel = image[:, :, 3]
    if np.all(alpha_channel == 255):
        warnings.append("🔲 Transparency channel present but not used (fully opaque).")

# 4. Contours check
if not significant_contours:
    errors.append("🔍 No significant contours detected.")
elif len(significant_contours) < 2:
    warnings.append("✨ Very few visual features detected. Check logo design complexity.")

# 5. Color presence check
if len(image.shape) == 3 and image.shape[2] >= 3:
    if np.allclose(image[..., 0], image[..., 1]) and np.allclose(image[..., 1], image[..., 2]):
        warnings.append("🎨 Image appears to be grayscale. Logos should usually have brand colors.")

# 6. File format warning
ext = os.path.splitext(file_path)[1].lower()
if ext not in ['.png', '.svg']:
    warnings.append(f"📂Logo file is'{ext}'.Consider using'.png'or'.svg'for transparency and scalability.")

# 7. Visual debug output
debug_image = image.copy()
if len(debug_image.shape) == 2:  # grayscale, convert to color
    debug_image = cv2.cvtColor(debug_image, cv2.COLOR_GRAY2BGR)
cv2.drawContours(debug_image, significant_contours, -1, (0, 255, 0), 2)
debug_output_path = os.path.join(os.path.dirname(file_path), "debug_output.png")
cv2.imwrite(debug_output_path, debug_image)


# --- Output Results ---
print("\n=== LOGO ANALYSIS REPORT ===")
if errors:
    print("🚨 ISSUES:")
    for e in errors:
        print(f"- {e}")
if warnings:
    print("\n⚠️ WARNINGS:")
    for w in warnings:
        print(f"- {w}")
if not errors and not warnings:
    print("✅ Logo passed all checks successfully!")

def detect_low_contrast(image, threshold=30):
    gray = cv2.cvtColor(image, cv2.COLOR_BGR2GRAY)
    min_val, max_val = np.min(gray), np.max(gray)
    contrast = max_val - min_val
    return contrast < threshold, contrast

def detect_off_center(image):
    h, w = image.shape[:2]
    gray = cv2.cvtColor(image, cv2.COLOR_BGR2GRAY)
    moments = cv2.moments(gray)
    if moments["m00"] == 0:
        return True, (w // 2, h // 2)
    cx = int(moments["m10"] / moments["m00"])
    cy = int(moments["m01"] / moments["m00"])
    center = (w // 2, h // 2)
    dist = np.sqrt((cx - center[0])**2 + (cy - center[1])**2)
    return dist > min(w, h) * 0.05, (cx, cy)

def analyze_logo(image_path):
    image = cv2.imread(image_path)
    annotated = image.copy()
    results = []

    # 1. Low Contrast
    low_contrast, contrast_val = detect_low_contrast(image)
    if low_contrast:
        results.append(f"[Low Contrast] Detected contrast: {contrast_val:.2f}")
        cv2.putText(annotated, "Low Contrast!", (10, 30), cv2.FONT_HERSHEY_SIMPLEX, 1, (0,0,255), 2)

    # 2. Off-Center Detection
    off_center, (cx, cy) = detect_off_center(image)
    if off_center:
        results.append(f"[Alignment] Logo elements are off-center (cx={cx}, cy={cy})")
        cv2.drawMarker(annotated, (cx, cy), (0, 0, 255), markerType=cv2.MARKER_CROSS, markerSize=20, thickness=2)
        h, w = image.shape[:2]
        cv2.drawMarker(annotated, (w//2, h//2), (0, 255, 0), markerType=cv2.MARKER_STAR, markerSize=20, thickness=2)

    # Final save
    output_path = image_path.replace(".png", "_annotated.png")
    cv2.imwrite(output_path, annotated)

    return results, os.path.basename(output_path)

if __name__ == "__main__":
    if len(sys.argv) < 2:
        print("Usage: analyze_logo.py <image_path>")
        sys.exit(1)

    logo_path = sys.argv[1]
    findings, annotated_img = analyze_logo(logo_path)

    print("🔍 Detected Logo Issues:\n")
    if not findings:
        print("✅ No major issues detected. Logo looks good!")
    else:
        for item in findings:
            print("• " + item)

    print(f"\n📌 Annotated Image Saved: {annotated_img}")


# Summary
print("\n--- Summary ---")
print(f"Path: {file_path}")
print(f"Dimensions: {image.shape[1]}x{image.shape[0]} px")
print(f"Channels: {image.shape[2] if len(image.shape) == 3 else 1} (Alpha: {'Yes' if has_alpha else 'No'})")
print(f"Sharpness Score: {laplacian_var:.2f}")
print(f"Contours Found: {len(significant_contours)}")
print(f"🖼️ Contour Debug Image: {debug_output_path}")


# You can add a real contrast checker if needed; set manually for now
contrast_issue = False  

# Generate AI Suggestions
ai_suggestions = suggest_design_improvements(
    has_alpha=has_alpha,
    contrast_issue=contrast_issue,
    sharpness=laplacian_var,
    contour_count=len(significant_contours)
)

print("\n🤖 AI DESIGN SUGGESTIONS:")
for suggestion in ai_suggestions:
    print(f"- {suggestion}")
