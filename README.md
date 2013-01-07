## Synopsis

"Open Mappr" is a product of Vibrant Data Labs as part of a collaborative effort with: 

Eric L. Berlow (Vibrant Data Labs) - original idea and approach and research lead

David Gurman (Brainvise) - creative direction and design

Sundev Lohr (Brainvise) - programming and development

Brandon Barnett, Richard Beckwith, John Sherry (Intel Labs) - advice and research partnership


Open Mappr software enables multiple independent users to collaboratively build a network. The software takes users through 3 stages. Node Listing (where multiple users can submit entities to be used as nodes in the network), Node Curation (where the raw submitted node list can be cleaned up and edited), and Remote Link Mapping (where multiple users can independently select a subset of the nodes to focus on and  map directed links between them). The Node Listing and Node Curation stages can be skipped by simply uploading a list of nodes and node attributes from a spreadsheet. Open Mappr outputs the data in different formats to be easily imported into standard network visualization and analysis packages (e.g., Gephi).  

Currently the software is in an extremely alpha phase, but we plan on organizing and restructuring the software and opening it up with an API in the near future. We are using a simple BSD license because we realize you might want to keep some modifications proprietary, but we strongly encourage you to share alike if you can. 

## Installation

This project was built using Codeigniter (a php framework) and jQuery (a javascript framework). If you are not familiar with either of these, please take the time to do some research. 

The mysql database is located in the root folder and named "mappr_db.sql". Install this database on your server.

Edit the "database.php" file located in the "application->config" folder to reflect the name of the newly created database as well as your login information. Specifically edit the fields corresponding to "&lt;USER NAME&gt;", "&lt;USER PASSWORD&gt;", and "&lt;DATABASE NAME&gt;".

Upload all files located in the "Open Mappr" directory to the root directory for the domain.

Visit the domain where you've uploaded the files and login with the following:

user: new@vibrantdatalabs.org

password: abcd1234

Create a new Admin User. Check your email to confirm registration for this new admin user and finish setting up your account by visiting the link in the email. 

Delete the admin user: new@vibrantdatalabs.org

## API Reference

Coming Soon.

## Contributors

Vibrant Data Labs http://vibrantdatalabs.org

Brainvise http://brainvise.com

## License

This application is licensed under the Simplified BSD License http://en.wikipedia.org/wiki/BSD_licenses
