#Developer - Ajaypal Singh
#Email - er.ajaypal@gmail.com
#Contact - 0452566855

#Description
The csv export functionality built is in response to the code test conducted by Catch as per the instructions provided via email.

#Installing steps
1. Install the project from git using following link
   	git clone https://goodajay@bitbucket.org/goodajay/csv-export.git 

2. In command prompt, go to the project directory and run the following composer command
	composer install

	Please note - You need to have composer installed on the system

	This command is to install the framework and other dependencies required for the feature

3. Checkout to develop branch
4. In command prompt, run the following command to run the functionality
	php bin\console app:export-orders-csv

	The feature will 
	a. read the content from the json file being provided as url
	b. copy/download the json file locally under the following path
		var/downloads/orders.jsonl

	c. read the content of the downloaded file and then create the required orders.csv under the same path
		var/downloads/orders.csv

#Extra features
Google geocode api is being used to fetch the latitude and longitude of the address

