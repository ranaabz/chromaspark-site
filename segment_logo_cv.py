import cv2
import numpy as np
import sys
import os

def detect_blur(gray, ksize=31, thresh=100.0):
    """Return mask of regions where Laplacian variance < thresh."""
    lap = cv2.Laplacian(gray, cv2.CV_64F)
    var = cv2.GaussianBlur(lap**2, (ksize, ksize), 0)
    # Low‐variance regions → more blur
    return (var < thresh).astype(np.uint8) * 255

def detect_low_contrast(gray, window=50, thresh=15):
    """Slide a window and flag cells with contrast < thresh."""
    h, w = gray.shape
    mask = np.zeros_like(gray, dtype=np.uint8)
    for y in range(0, h, window):
        for x in range(0, w, window):
            patch = gray[y:y+window, x:x+window]
            if patch.size == 0: continue
            # contrast = max – min
            if patch.max() - patch.min() < thresh:
                mask[y:y+window, x:x+window] = 255
    return mask

def detect_jaggies(gray, thresh1=50, thresh2=150, area_thresh=500):
    """Find dense edge clusters (pixelation artifacts)."""
    edges = cv2.Canny(gray, thresh1, thresh2)
    # Dilate to merge nearby edges
    kernel = np.ones((3,3), np.uint8)
    dil = cv2.dilate(edges, kernel, iterations=1)
    # Find connected regions
    labels = cv2.connectedComponentsWithStats(dil, 8, cv2.CV_32S)
    mask = np.zeros_like(gray, dtype=np.uint8)
    # Flag any region with large bounding box but low fill → jagged edges
    _, _, stats, _ = labels
    for stat in stats[1:]:
        x, y, w, h, area = stat
        if area > area_thresh and area / (w*h) < 0.5:
            mask[y:y+h, x:x+w] = 255
    return mask

def overlay_masks(orig, masks, colors):
    """Overlay each single‐channel mask onto orig BGR with given BGR color."""
    overlay = orig.copy()
    for mask, col in zip(masks, colors):
        # create colored version of mask
        colored = np.zeros_like(orig)
        colored[mask>0] = col
        # alpha blend
        overlay = cv2.addWeighted(overlay, 1.0, colored, 0.4, 0)
    return overlay

def add_legend(image, items, start_x=10, start_y=30, spacing=30):
    """
    Draws a legend on the image with colored boxes and labels.
    """
    font = cv2.FONT_HERSHEY_SIMPLEX
    font_scale = 0.8
    thickness = 2
    text_color = (0, 0, 0)  # Black text for visibility

    for i, (label, color) in enumerate(items):
        y = start_y + i * spacing
        # Draw color box
        cv2.rectangle(image, (start_x, y - 10), (start_x + 20, y + 10), color, -1)
        # Draw label
        cv2.putText(image, label, (start_x + 30, y + 5), font, font_scale, text_color, thickness, cv2.LINE_AA)
    return image


def main(image_path):
    img = cv2.imread(image_path)
    gray = cv2.cvtColor(img, cv2.COLOR_BGR2GRAY)

    # 1) Blur mask
    blur_mask = detect_blur(gray, ksize=31, thresh=100.0)

    # 2) Low-contrast mask
    contrast_mask = detect_low_contrast(gray, window=50, thresh=15)

    # 3) Jagged/pixelation mask
    jaggy_mask = detect_jaggies(gray, thresh1=50, thresh2=150, area_thresh=500)

    # Overlay: red for blur, yellow for low contrast, blue for jaggies
    overlay = overlay_masks(img, 
                            [blur_mask, contrast_mask, jaggy_mask],
                            [(0,0,255), (0,255,255), (255,0,0)])
    
    legend_items = [
        ("Blur", (0, 0, 255)),            # Red
        ("Low Contrast", (0, 255, 255)),  # Yellow
        ("Jagged Edges", (255, 0, 0))     # Blue
    ]
    overlay = add_legend(overlay, legend_items)

    # Save result
    out_dir = "analysis/output"
    os.makedirs(out_dir, exist_ok=True)
    base = os.path.splitext(os.path.basename(image_path))[0]
    out_path = os.path.join(out_dir, base + "_errors.png")
    cv2.imwrite(out_path, overlay)
    print(out_path)

if __name__ == "__main__":
    main(sys.argv[1])
