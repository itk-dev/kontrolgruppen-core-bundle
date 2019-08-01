# kontrolgruppen-core-bundle

Kontrolgruppen core bundle.

## Building js and css

Submodule of Kontrolgruppen. Run `yarn install` in your project root to build assets.

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
bin/console bin/console kontrolgruppen:user:login --help
```

for details.
