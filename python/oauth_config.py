from authomatic.providers import oauth2
import config

class Camdram(oauth2.OAuth2):
    user_authorization_url = config.CAMDRAM_URL + '/oauth/v2/auth'
    access_token_url = config.CAMDRAM_URL + '/oauth/v2/token'
    user_info_url = config.CAMDRAM_URL + '/auth/account.json'
    type_id = 9999

    @staticmethod
    def _x_user_parser(user, data):
        user.id = data.get('id')
        user.last_name = data.get('name')
        return user

PROVIDER_IP_MAP = [Camdram]

CONFIG = {
    'camdram': {
        'class_': Camdram,
        'consumer_key' : config.API_KEY,
        'consumer_secret' : config.API_SECRET,
    }
}
