import requests, os, bs4

url = 'https://editorial.rottentomatoes.com/guide/best-horror-movies-by-year-since-1920/'

# Download the page.
response = requests.get(url)
response.raise_for_status()

soup = bs4.BeautifulSoup(response.text, 'html.parser')

movie_list = soup.select('.countdown-item')
if movie_list== []:
    print('Could not find movies.')
for movie in movie_list:
    image_list = movie.select('.article_movie_poster img')
    image_url = image_list[0].get('src')
    # Download the image.
    image = requests.get(image_url)
    image.raise_for_status()

    title_list = movie.select('.article_movie_title a')
    title = title_list[0].text
    title = title.replace(':','')

    # Save the image
    poster_name = title + '.png'
    imageFile = open(os.path.join('images', poster_name),'wb')
    for chunk in image.iter_content(100000):
        imageFile.write(chunk)
    imageFile.close()
