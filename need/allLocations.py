# import modules
import csv
import json

# define input and output files
csvFilePath = "locations.csv"
jsonFilePath = "allLocations.js"

# create list to store store location data
allLocations = []

# append data as dictionaries into list
with open(csvFilePath) as csvFile:
    csvReader = csv.DictReader(csvFile)
    for row in csvReader:
        allLocations.append(row)
            
# write out data to JSON file
with open(jsonFilePath, 'w') as jsonFile:
    jsonFile.write(json.dumps(allLocations, indent=4))
