body='{
"request": {
"branch":"master",
"message":"Triggered by kinikit-core"
}}'

curl -X POST \
   -H "Content-Type: application/json" \
   -H "Travis-API-Version: 3" \
   -H "Authorization: token $TRAVIS_API_TOKEN" \
   -d "$body" \
   https://api.travis-ci.com/repo/OxfordInfoLabs%2F$1/requests

