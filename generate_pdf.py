import os
from fpdf import FPDF
from datetime import datetime

def generate_logo_report(pdf_path, summary_text, image_path):
    pdf = FPDF()
    pdf.add_page()
    pdf.set_font("Arial", size=12)
    pdf.cell(200, 10, txt="Chroma Spark Logo Analysis Report", ln=True, align='C')
    pdf.ln(10)

    for line in summary_text.split('\n'):
        pdf.multi_cell(0, 10, txt=line)

    if os.path.exists(image_path):
        pdf.image(image_path, w=150)

    pdf.ln(10)
    pdf.set_font("Arial", size=10)
    pdf.cell(0, 10, f"Generated on {datetime.now().strftime('%Y-%m-%d %H:%M')}", ln=True)

    pdf.output(pdf_path)
