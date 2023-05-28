# Genesis_test_task
This is a test task for Genesis software engineering school.

In order to send email you should define your Mailjet's API_KEY, API_SECRET and email.

## Docker:

Build:

```docker build -t 'image-name' .```

Run:

```docker run -p 8000:8000 'image-name'```

Result:
http://localhost:8000/

### Logic

Getting the bitcoin price is done using coingeco API.


Emails are sent using Mailjet API.
