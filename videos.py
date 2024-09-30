import requests
import mysql.connector
import time

YOUTUBE_API_KEY = 'AIzaSyCxMV3AFFq4RWlyoZMhA4RkXsbTmeZ4Ix8'
YOUTUBE_SEARCH_URL = 'https://www.googleapis.com/youtube/v3/search'
YOUTUBE_VIDEO_URL = 'https://www.youtube.com/watch?v='
db = mysql.connector.connect(
    host="localhost",
    user="root",
    password="",
    database="search_app"
)
cursor = db.cursor()

def fetch_videos(query, page_token=None):
    params = {
        'part': 'snippet',
        'maxResults': 50,  
        'q': query,  
        'type': 'video',
        'key': YOUTUBE_API_KEY,
        'pageToken': page_token
    }
    response = requests.get(YOUTUBE_SEARCH_URL, params=params)
    if response.status_code == 200:
        return response.json()
    else:
        print(f"Error fetching videos for query '{query}': Status code {response.status_code}")
        return None

def save_video_to_db(video):
    video_id = video['id']['videoId']
    title = video['snippet']['title']
    description = video['snippet']['description']
    url = f"{YOUTUBE_VIDEO_URL}{video_id}"
    published_at = video['snippet']['publishedAt']

    cursor.execute('SELECT COUNT(*) FROM videos WHERE id = %s', (video_id,))
    if cursor.fetchone()[0] == 0:
        cursor.execute('''
            INSERT INTO videos (id, title, description, url, published_at)
            VALUES (%s, %s, %s, %s, %s)
        ''', (video_id, title, description, url, published_at))
        db.commit()

topics = [
    'Python programming tutorials',
    'Machine learning tutorials',
    'Data science courses',
    'Tech reviews',
    'Yoga workouts',
    'Travel vlogs',
]

total_videos = 10000
fetched_videos = 0

for topic in topics:
    page_token = None
    while fetched_videos < total_videos:
        data = fetch_videos(topic, page_token)
        if data is None:
            break

        for video in data['items']:
            save_video_to_db(video)
            fetched_videos += 1
            if fetched_videos >= total_videos:
                break

        page_token = data.get('nextPageToken')
        if not page_token:
            break

        print(f'{fetched_videos} videos processed for topic: {topic}')
        time.sleep(1)  

cursor.close()
db.close()
