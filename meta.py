import os
import requests
from googleapiclient.discovery import build
from PyPDF2 import PdfReader
from pdfminer.high_level import extract_text
from pdfminer.pdfparser import PDFSyntaxError
import mysql.connector

API_KEY = 'AIzaSyAwb_fikXHQDvjsH6wU2qlFa11KIBgfow0'
CSE_ID = '835ef5f39d3c241bf'

def fetch_pdfs_from_google(query, num_pdfs=30):
    service = build("customsearch", "v1", developerKey=API_KEY)
    pdf_urls = []
    start_index = 1

    while len(pdf_urls) < num_pdfs and start_index < 100:
        res = service.cse().list(q=query, cx=CSE_ID, fileType='pdf', num=min(num_pdfs-len(pdf_urls), 10), start=start_index).execute()
        for item in res.get('items', []):
            link = item.get('link')
            if link.endswith('.pdf'):
                pdf_urls.append(link)
        start_index += 10

    print(f"Found {len(pdf_urls)} PDFs for query: {query}")
    return pdf_urls[:num_pdfs]

def download_pdfs(pdf_urls, download_dir="pdfs"):
    if not os.path.exists(download_dir):
        os.makedirs(download_dir)

    downloaded_files = []
    for url in pdf_urls:
        try:
            response = requests.get(url, stream=True)
            if response.status_code == 200:
                filename = url.split('/')[-1]
                file_path = os.path.join(download_dir, filename)
                with open(file_path, 'wb') as f:
                    for chunk in response.iter_content(chunk_size=8192):
                        f.write(chunk)
                if validate_pdf(file_path):
                    downloaded_files.append((filename, file_path, url))
                    print(f"Successfully downloaded and validated: {file_path}")
                else:
                    os.remove(file_path)
                    print(f"Removed invalid PDF: {file_path}")
            else:
                print(f"Failed to download: {url} (status code: {response.status_code})")
        except Exception as e:
            print(f"Error downloading {url}: {e}")

    return downloaded_files

def validate_pdf(file_path):
    try:
        reader = PdfReader(file_path)
        extract_text(file_path)
        return True
    except (PDFSyntaxError, Exception) as e:
        print(f"Error reading {file_path}: {e}")
        return False

def insert_pdfs_to_db(pdfdb):
    db = mysql.connector.connect(
        host="localhost",
        user="root",
        password="",
        database="search_app"
    )
    cursor = db.cursor()
    
    for filename, file_path, url in pdfdb:
        title = filename
        query = "INSERT INTO pdfdb (title, file_path, url) VALUES (%s, %s, %s)"
        cursor.execute(query, (title, file_path, url))
    
    db.commit()
    cursor.close()
    db.close()
    print("PDFs inserted into database successfully.")

if __name__ == "__main__":
    queries = ["quantum computing", "science", "yoga", "health"]
    for query in queries:
        pdf_urls = fetch_pdfs_from_google(query)
        downloaded_pdfs = download_pdfs(pdf_urls)
        insert_pdfs_to_db(downloaded_pdfs)





