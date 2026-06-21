# Deploying to Render + Aiven (free)

This replaces the Railway deployment. Three free accounts are involved:
**Render** (runs the app), **Aiven** (MySQL database), **Cloudflare R2** (file storage for uploaded documents and certificates, so they survive restarts).

The app already has a `Dockerfile` and `.dockerignore` ready to go — Render builds straight from these.

---

## 1. Cloudflare R2 (file storage)

1. Sign up at [dash.cloudflare.com](https://dash.cloudflare.com) → **R2 Object Storage** → Create bucket (e.g. `eservices-uploads`).
2. On the bucket → **Settings** → **Public Access** → enable it, copy the public URL (looks like `https://pub-xxxxxxxx.r2.dev`).
3. Go to **R2** → **Manage API Tokens** → create a token with **Object Read & Write** permission on this bucket. Copy the **Access Key ID**, **Secret Access Key**, and the **Account ID** (the endpoint is `https://<account_id>.r2.cloudflarestorage.com`).

You'll need: Access Key ID, Secret Access Key, bucket name, endpoint URL, public URL.

## 2. Aiven (MySQL database)

1. Sign up at [aiven.io](https://aiven.io) → create a new service → **MySQL** → select the **Free** plan.
2. Once it's running, open the service → **Overview** tab → copy: **Host**, **Port**, **User**, **Password**, **Database name** (default is `defaultdb`).
3. Same page → download the **CA Certificate** (it's required — Aiven only accepts SSL connections). Save it somewhere you can find it.

## 3. Render (the app itself)

1. Sign up at [render.com](https://render.com) → **New** → **Web Service** → connect this GitHub repo.
2. Render should detect the `Dockerfile` automatically (Environment: **Docker**). Region: pick whichever is closest to you.
3. Plan: **Free**.
4. Before first deploy, add a **Secret File** (Render dashboard → your service → **Environment** → **Secret Files**):
   - Filename: `/app/storage/aiven-ca.pem`
   - Contents: paste the Aiven CA certificate you downloaded in step 2.
5. Add these **Environment Variables**:

   | Key | Value |
   |---|---|
   | `APP_NAME` | `E-Services Platform` |
   | `APP_ENV` | `production` |
   | `APP_KEY` | *(generate locally with `php artisan key:generate --show`, paste the output)* |
   | `APP_DEBUG` | `false` |
   | `APP_URL` | your Render URL, e.g. `https://eservices.onrender.com` |
   | `APP_LOCALE` | `en` |
   | `DB_CONNECTION` | `mysql` |
   | `DB_HOST` | *(from Aiven)* |
   | `DB_PORT` | *(from Aiven)* |
   | `DB_DATABASE` | *(from Aiven)* |
   | `DB_USERNAME` | *(from Aiven)* |
   | `DB_PASSWORD` | *(from Aiven)* |
   | `MYSQL_ATTR_SSL_CA` | `/app/storage/aiven-ca.pem` |
   | `SESSION_DRIVER` | `database` |
   | `CACHE_STORE` | `database` |
   | `QUEUE_CONNECTION` | `database` |
   | `FILESYSTEM_PUBLIC_DRIVER` | `s3` |
   | `FILESYSTEM_PRIVATE_DRIVER` | `s3` |
   | `AWS_ACCESS_KEY_ID` | *(from R2)* |
   | `AWS_SECRET_ACCESS_KEY` | *(from R2)* |
   | `AWS_DEFAULT_REGION` | `auto` |
   | `AWS_BUCKET` | *(your R2 bucket name)* |
   | `AWS_ENDPOINT` | *(R2 endpoint URL)* |
   | `AWS_URL` | *(R2 public bucket URL)* |
   | `AWS_USE_PATH_STYLE_ENDPOINT` | `true` |
   | `MAIL_MAILER` | `log` *(or your real mail provider — see existing `.env` for Resend/Brevo options)* |
   | `GEMINI_API_KEY` | *(your Gemini key, for the chatbot)* |

   Anything else already in your local `.env` that this app uses (Google Maps, Stripe, crypto wallets, Pusher, Google OAuth) — copy those over too if you want those features working live.

6. Click **Create Web Service**. Render will build the Docker image, then run the container, which automatically runs migrations and starts the app (see the `Dockerfile`'s `CMD`).

## 4. First boot

The container runs `php artisan migrate --force` on every start — this creates all your tables on the fresh Aiven database automatically. If you also want the real municipal services seeded in, run once from your local machine pointed at the Aiven database (or use Render's **Shell** tab):

```
php artisan db:seed --class=RealMunicipalServicesSeeder
```

## Known trade-off of the free tier

Render's free web service **sleeps after ~15 minutes of no traffic** and takes 30-60 seconds to wake up on the next visit. For a live demo, open the site a minute or two before you need it so it's already awake.

## If something doesn't boot

Check Render's **Logs** tab first — almost every first-deploy issue is either a missing environment variable or the Aiven SSL certificate path being wrong.
