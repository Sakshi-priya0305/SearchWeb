import requests
import mysql.connector
import time


UNSPLASH_ACCESS_KEY = 'XSORlbLX0CitlWO84Y8G3Dh05eD6B_edqdrza4Da9EA'
UNSPLASH_SEARCH_URL = 'https://api.unsplash.com/search/photos'


db = mysql.connector.connect(
    host="localhost",
    user="root",
    password="",
    database="search_app"
)
cursor = db.cursor()


cursor.execute('''
CREATE TABLE IF NOT EXISTS images (
    id VARCHAR(255) PRIMARY KEY,
    description TEXT,
    alt_description TEXT,
    url VARCHAR(255)
)
''')

def fetch_images(query, page, per_page):
    headers = {'Authorization': f'Client-ID {UNSPLASH_ACCESS_KEY}'}
    params = {'query': query, 'page': page, 'per_page': per_page}
    response = requests.get(UNSPLASH_SEARCH_URL, headers=headers, params=params)
    if response.status_code == 200:
        try:
            return response.json().get('results', [])
        except ValueError:
            print(f"Error parsing JSON response for query '{query}' on page {page}")
            return []
    elif response.status_code == 403:
        print(f"Access denied for query '{query}' on page {page}: Status code 403")
        return []
    else:
        print(f"Error fetching query '{query}' on page {page}: Status code {response.status_code}")
        return []

def save_image_to_db(image):
    cursor.execute('SELECT COUNT(*) FROM images WHERE id = %s', (image['id'],))
    if cursor.fetchone()[0] == 0:
        cursor.execute('''
            INSERT INTO images (id, description, url)
            VALUES (%s, %s, %s)
        ''', (image['id'], image.get('description', ''), image['urls']['regular']))
        db.commit()


keywords = ['animal', 'snacks', 'friends','family', 'universe']


per_page = 25  
retry_limit = 5
retry_count = 0
max_page = 10

for keyword in keywords:
    for page in range(1, max_page + 1):
        images = fetch_images(keyword, page, per_page)
        if not images:
            retry_count += 1
            if retry_count >= retry_limit:
                print(f"Stopping for keyword '{keyword}' at page {page} due to repeated errors")
                break
            print(f"Retrying keyword '{keyword}' at page {page}")
            continue
        for image in images:
            save_image_to_db(image)
        print(f"Keyword '{keyword}', Page {page} processed.")
        time.sleep(1)  

cursor.close()
db.close()





