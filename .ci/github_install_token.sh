#!/usr/bin/env bash
# github_install_token.sh

set -o pipefail

# Function to handle errors
error_exit() {
    echo "Error: $1"
    exit 1
}

# Get pem from env variable if not provided as argument
pem=${1:-$GITHUB_APP_PEM}
if [ -z "$pem" ]; then
    error_exit "Private key is not provided. Set it as an argument or via the GITHUB_APP_PEM environment variable."
fi

# Get client_id from env variable if not provided as argument
client_id=${2:-$GITHUB_APP_CLIENT_ID}
if [ -z "$client_id" ]; then
    error_exit "Client ID is not provided. Set it as an argument or via the GITHUB_APP_CLIENT_ID environment variable."
fi

# Get installation_id from env variable if not provided as argument
installation_id=${3:-$GITHUB_APP_INSTALLATION_ID}
if [ -z "$installation_id" ]; then
    error_exit "Installation ID is not provided. Set it as an argument or via the GITHUB_APP_INSTALLATION_ID environment variable."
fi

now=$(date +%s) || error_exit "Failed to get current time."
iat=$((${now} - 60)) || error_exit "Failed to calculate iat."
exp=$((${now} + 600)) || error_exit "Failed to calculate exp."

b64enc() { openssl base64 | tr -d '=' | tr '/+' '_-' | tr -d '\n'; }

header_json='{
    "typ":"JWT",
    "alg":"RS256"
}'

# Header encode
header=$( echo -n "${header_json}" | b64enc ) || error_exit "Failed to encode header."

payload_json='{
    "iat":'"${iat}"',
    "exp":'"${exp}"',
    "iss":"'"${client_id}"'"
}'

# Payload encode
payload=$( echo -n "${payload_json}" | b64enc ) || error_exit "Failed to encode payload."

# Signature
header_payload="${header}"."${payload}"
signature=$(
    openssl dgst -sha256 -sign <(echo -n "${pem}") \
    <(echo -n "${header_payload}") | b64enc
) || error_exit "Failed to sign JWT."

# Create JWT
JWT="${header_payload}"."${signature}"

create_installation_token() {
    response=$(curl -s --request POST \
        --url "https://api.github.com/app/installations/$installation_id/access_tokens" \
        --header "Accept: application/vnd.github+json" \
        --header "Authorization: Bearer $JWT" \
        --header "X-GitHub-Api-Version: 2022-11-28") || error_exit "Failed to request installation token."

    token=$(echo "$response" | jq -r '.token')
    if [ "$token" == "null" ]; then
        error_exit "Failed to create installation token: $(echo "$response" | jq -r '.message')"
    fi

    echo "$token"
}

install_token=$(create_installation_token)
echo "$install_token"
