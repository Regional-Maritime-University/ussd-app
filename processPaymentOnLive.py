import json
import redis
import requests
import logging

# Configure logging
logging.basicConfig(level=logging.INFO)
logger = logging.getLogger(__name__)

channel_name = 'livePaymentChannel'
target_url = 'https://pay.rmuictonline.com/pay'

redis_client = redis.StrictRedis(host='localhost', port=6379, db=0)
pubsub = redis_client.pubsub()
pubsub.subscribe(channel_name)

for message in pubsub.listen(timeout=3000):
    if message['type'] == 'message':
        payment_data = json.loads(message['data'])
        headers = {'Content-Type': 'application/json'}  # Set the Content-Type header
        try:
            response = requests.post(target_url, data=json.dumps(payment_data), headers=headers)
            if response.status_code == 200 or response.status_code == 201:
                logger.info("Status: Success %s", payment_data)
            else:
                logger.error(
                    "Status: Error %s. Error message: %s. Response: %s",
                    payment_data, response.text, response.status_code,
                )
        except requests.RequestException as e:
            logger.error("Error making the request: %s", str(e))
