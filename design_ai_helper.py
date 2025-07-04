def suggest_design_improvements(has_alpha, contrast_issue, sharpness, contour_count):
    suggestions = []

    if not has_alpha:
        suggestions.append("💡 Add transparency for better adaptability on backgrounds.")
    if contrast_issue:
        suggestions.append("🎨 Improve color contrast for better visibility.")
    if sharpness < 100:
        suggestions.append("🔧 Logo appears blurry. Export at higher resolution.")
    if contour_count < 3:
        suggestions.append("✨ Logo design appears very simple. Consider adding visual elements.")

    if not suggestions:
        suggestions.append("✅ Your logo meets our design quality checks!")

    return suggestions
