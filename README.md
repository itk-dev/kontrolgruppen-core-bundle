# kontrolgruppen-core-bundle

Kontrolgruppen core bundle.

## Building js and css

Submodule of Kontrolgruppen. Run `yarn install` in your project root to build assets.

## Environment
Set the site name in the .env file:
```ini
SITE_NAME=your-site-name
```

## SAML

Configuration of SAML login must be done with configuration under `kontrolgruppen_core.saml`.

Example configuration:

```yaml
# @see https://symfony.com/doc/current/configuration/environment_variables.html#environment-variable-processors
parameters:
    env(ROUTER_REQUEST_CONTEXT_BASE_URL): ''
    router.request_context.scheme: '%env(ROUTER_REQUEST_CONTEXT_SCHEME)%'
    router.request_context.host: '%env(ROUTER_REQUEST_CONTEXT_HOST)%'
    router.request_context.base_url: '%env(ROUTER_REQUEST_CONTEXT_BASE_URL)%'
    base_url: '%router.request_context.scheme%://%router.request_context.host%%router.request_context.base_url%'

    env(SAML_SP_CRT_FILE): '%kernel.project_dir%/saml/sp/sp.crt'
    env(SAML_SP_KEY_FILE): '%kernel.project_dir%/saml/sp/sp.key'
    env(SAML_IDP_CONFIG_FILE): '%kernel.project_dir%/saml/idp/idp.xml'
    saml_sp_crt: '%env(file:resolve:SAML_SP_CRT_FILE)%'
    saml_sp_key: '%env(file:resolve:SAML_SP_KEY_FILE)%'
    saml_idp_config_file: '%env(resolve:SAML_IDP_CONFIG_FILE)%'

kontrolgruppen_core:
    …

    saml:

        php_saml_settings:
            # https://github.com/onelogin/php-saml#settings
            strict:              true
            debug:               true
            sp:
                entityId: '%base_url%'
                assertionConsumerService:
                    url: '%base_url%/saml/acs'
                    binding: 'urn:oasis:names:tc:SAML:2.0:bindings:HTTP-POST'
                singleLogoutService:
                    url: '%base_url%/saml/sls'
                    binding: 'urn:oasis:names:tc:SAML:2.0:bindings:HTTP-Redirect'
                NameIDFormat: 'urn:oasis:names:tc:SAML:1.1:nameid-format:emailAddress'
                x509cert: '%saml_sp_crt%'
                privateKey: '%saml_sp_key%'

            # Read IdP configuration from a file.
            idp: '%saml_idp_config_file%'

            # Advanced settings (https://github.com/onelogin/php-saml#settings)
            compress:
                requests: true
                responses: true
            security:
                nameIdEncrypted: false
                authnRequestsSigned: false
                logoutRequestSigned: false
                logoutResponseSigned: false
                signMetadata: false
                wantMessagesSigned: false
                wantAssertionsEncrypted: false
                wantAssertionsSigned: false
                wantNameId: true
                wantNameIdEncrypted: false
                requestedAuthnContext: true
                wantXMLValidation: true
                relaxDestinationValidation: false
                signatureAlgorithm: http://www.w3.org/2001/04/xmldsig-more#rsa-sha256
                digestAlgorithm: http://www.w3.org/2001/04/xmlenc#sha256
                lowercaseUrlencoding: false
            contactPerson:
                technical:
                    givenName: ITK Development
                    emailAddress: itk-dev@aarhus.dk
                support:
                    givenName: ITK Development
                    emailAddress: itk-dev@aarhus.dk
            organization:
                en-US:
                    name: Kontrolgruppen
                    displayname: Kontrolgruppen
                    url: https://kontrolgruppen.example.com

            user_roles:
                attribute: http://schemas.microsoft.com/ws/2008/06/identity/claims/role
                # Map from ADFS stuff to Symfony roles
                fields:
                    # Common Name
                    CN:
                        DG-Right-Kontrolsystem-Admin: ROLE_ADMIN
                        DG-Right-Kontrolsystem-Sagsbehandler: ROLE_SAGSBEHANDLER
```

To make this example configuration work, you must create a key and a certificate
(change `--subj` to match your actual setup):

```sh
mkdir -p saml/{idp,sp}
openssl req -x509 -sha256 -nodes -days 1460 -newkey rsa:2048 \
    -keyout saml/sp/sp.key \
    -out saml/sp/sp.crt \
    -subj "/C=DK/L=Aarhus/O=Kontrolgruppen/CN=kontrolgruppen.example.com/emailAddress=info@kontrolgruppen.example.com"
```

and download metadata from your identity provider (IdP) to `saml/idp/idp.xml`.

## Sign in from command line

Rather than signing in via SAML, you can get a sign in url from the command line. Run

```sh
bin/console kontrolgruppen:user:login --help
```

for details.

## Business Intelligence™

Users with role `ROLE_BI` can access and download reports from `/bi/`. Reports
should be run regularly by using `cron` or similar means to execute the
`kontrolgruppen:report:export` cli command.

Run

```sh
bin/console kontrolgruppen:report:export --help
```

to see details on command parameters and available reports (defined in the
`kontrolgruppen_core.exports` configuration).

### Example

```sh
bin/console kontrolgruppen:report:export admin@example.com \
	'Kontrolgruppen\CoreBundle\Export\KL\Export' \
	--parameters='startdate=-1 month enddate=now'
```

### Protip

Use `--debug-parameters` to see what the parameter values actually are:

```
bin/console kontrolgruppen:report:export admin@example.com \
	'Kontrolgruppen\CoreBundle\Export\KL\Export' \
	--parameters='startdate=-1 month enddate=now' --debug-parameters
```

## CPR service

Make sure to set the following parameters in the .env file:

```ini
AZURE_TENANT_ID='xyz'
AZURE_APPLICATION_ID='xyz'
AZURE_CLIENT_SECRET='xyz'
AZURE_KEY_VAULT_SECRET='xyz'
AZURE_KEY_VAULT_SECRET_VERSION='xyz'

SERVICEPLATFORMEN_SERVICE_AGREEMENT_UUID='xyz'
SERVICEPLATFORMEN_USER_SYSTEM_UUID='xyz'
SERVICEPLATFORMEN_USER_UUID='xyz'

# The path to the wsdl most be relative to the project where this .env file is located
PERSON_BASE_DATA_EXTENDED_SERVICE_CONTRACT='path.to.wsdl'
PERSON_BASE_DATA_EXTENDED_SERVICE_ENDPOINT='https://xyz.com'
PERSON_BASE_DATA_EXTENDED_SERVICE_UUID='xyz'
```

And add the following to the parent bundles config/services.yaml:

```yaml
parameters:
    ...
    azure_tenant_id: '%env(AZURE_TENANT_ID)%'
    azure_application_id: '%env(AZURE_APPLICATION_ID)%'
    azure_client_secret: '%env(AZURE_CLIENT_SECRET)%'
    azure_key_vault_name: '%env(AZURE_KEY_VAULT_NAME)%'
    azure_key_vault_secret: '%env(AZURE_KEY_VAULT_SECRET)%'
    azure_key_vault_secret_version: '%env(AZURE_KEY_VAULT_SECRET_VERSION)%'
    serviceplatformen_service_agreement_uuid: '%env(SERVICEPLATFORMEN_SERVICE_AGREEMENT_UUID)%'
    serviceplatformen_user_system_uuid: '%env(SERVICEPLATFORMEN_USER_SYSTEM_UUID)%'
    serviceplatformen_user_uuid: '%env(SERVICEPLATFORMEN_USER_UUID)%'
    person_base_data_extended_service_contract: '%kernel.project_dir%/%env(PERSON_BASE_DATA_EXTENDED_SERVICE_CONTRACT)%'
    person_base_data_extended_service_endpoint: '%env(PERSON_BASE_DATA_EXTENDED_SERVICE_ENDPOINT)%'
    person_base_data_extended_service_uuid: '%env(PERSON_BASE_DATA_EXTENDED_SERVICE_UUID)%'
```

### Updating addresses on clients from the command lind
There is a console command available for updating client addresses based on
information from the CPR service:

```bash
bin/console kontrolgruppen:client:update

# To perform a dry-run (without updating the client) append the --dry-run flag
bin/console kontrolgruppen:client:update --dry-run

# Show info about how many clients and which clients that are updated:
# This can be used with the --dry-run flag as well.
bin/console kontrolgruppen:client:update -vvv
```

## Contributing

### Pull Request Process

Before creating a pull request make sure you have considered the following:
- Updating the README.md with details of changes that are relevant.
- Updating the CHANGELOG.md with new features/changes/bug fixes etc.

You may merge the Pull Request once you have the sign-off of one other developer, or if you do not have permission to do that, you may request the reviewer to merge it for you.
