import requests, os, bs4

url = 'https://xkcd.com'               # starting url
count = 5

for i in range(count):
    # Download the page.
    response = requests.get(url)
    response.raise_for_status()

    soup = bs4.BeautifulSoup(response.text, 'html.parser')

    # Find the URL of the comic image.
    comic_element = soup.select('#comic img')
    if comic_element== []:
        print('Could not find comic image.')
    else:
        comicUrl = 'https:' + comic_element[0].get('src')
        # Download the image.
        image = requests.get(comicUrl)
        image.raise_for_status()

    # Save the image
    comic_name = os.path.basename(comicUrl)
    imageFile = open(os.path.join('images', comic_name),'wb')
    for chunk in image.iter_content(100000):
        imageFile.write(chunk)
    imageFile.close()

    # Get the Prev button's url.
    prevLink = soup.select('a[rel="prev"]')[0]
    url = 'https://xkcd.com' + prevLink.get('href')
    if url.endswith('#'):
        break