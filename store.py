import os
import mysql.connector
from mysql.connector import Error

def insert_pdf_to_db(pdf_path, db_cursor):
    try:
        with open(pdf_path, 'rb') as file:
            file_data = file.read()
            file_name = os.path.basename(pdf_path)
            sql = "INSERT INTO pdfs (file_name, file_data) VALUES (%s, %s)"
            values = (file_name, file_data)
            db_cursor.execute(sql, values)
    except Exception as e:
        print(f"Error inserting {file_name} into database: {e}")

def connect_to_db():
    return mysql.connector.connect(
        host="localhost",
        user="root",
        password="",
        database="search_app"
    )

def insert_pdfs_from_directory(pdf_dir):
    try:
        db = connect_to_db()
        cursor = db.cursor()
        
        pdf_files = [f for f in os.listdir(pdf_dir) if f.endswith('.pdf')]
        for pdf_file in pdf_files:
            pdf_path = os.path.join(pdf_dir, pdf_file)
            insert_pdf_to_db(pdf_path, cursor)
        
        db.commit()
        print("PDF insertion into database completed.")
        
    except Error as e:
        print(f"Error connecting to MySQL: {e}")
    finally:
        if db.is_connected():
            cursor.close()
            db.close()

if __name__ == "__main__":
    pdf_dir = "pdfs"
    insert_pdfs_from_directory(pdf_dir)







    


