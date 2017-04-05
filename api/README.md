# Nigerian API
An API providing the list of Nigerian states and local government areas for development use.

## Usage

- **Fetch list of states**

  ````
  https://nigeria.herokuapp.com/api/v1/states/
  ````
  **Response**
  ````json
  {
    "status": true,
    "data": {
      "abia": {
        "id": "abia",
        "name": "Abia",
        "lgas": 16
      },
      "adamawa": {
        "id": "adamawa",
        "name": "Adamawa",
        "lgas": 20
      },
      ...
    }
  }
  ````

- **Fetch a single state**

  ````
  https://nigeria.herokuapp.com/api/v1/states/lagos
  ````
  **Response**
  ````json
  {
    "status": true,
    "data": {
      "id": "lagos",
      "name": "Lagos",
      "lgas": 19
    }
  }
  ````

- **Fetch a state's local government areas**

  ````
  https://nigeria.herokuapp.com/api/v1/states/kano/lgas
  ````
  **Response**
  ````json
  {
    "status": true,
    "data": {
      "albasu": {
        "id": "albasu",
        "name": "Albasu"
      },
      "bagwai": {
        "id": "bagwai",
        "name": "Bagwai"
      },
      ...
    }
  }
  ````
  
- **Update data**
  
    Only GET requests are allowed on the server to protect the integrity of the data. If there's a need for updates, clone this repo and send pull request.
    
    The data for the states and lgas is located at https://github.com/ezra-obiwale/Nigeria/blob/master/data/states.json
    
    The updates would be verified and merged into the app.
    
## Issues
Any issue/recommedation should be put up in GitHub issues tab and would be attended to promptly.

Viva Nigeria!
  
