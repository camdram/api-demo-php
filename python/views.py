from django.http import HttpResponse
from authomatic import Authomatic
from authomatic.adapters import DjangoAdapter

from oauth_config import CONFIG
import config

authomatic = Authomatic(CONFIG, 'jklshsdjklafhjklsdf')

def home(request):
    return HttpResponse('''
        <a href="login/">Login with Camdram</a>
    ''')

def login(request):
    response = HttpResponse()

    result = authomatic.login(DjangoAdapter(request, response), "camdram")

    if result:
	result.user.update()
        if result.user:
            response.write('<h1>Hello ' + result.user.name + '</h1>')
            response.write('<h2>My Shows:</h2><ul>')
            
            showsResponse = result.provider.access(config.CAMDRAM_URL + '/auth/account/shows.json')
            print showsResponse.data
            for show in showsResponse.data:
                response.write('<li>' + show['name'] + '</li>')
            response.write('</ul>')
    return response
