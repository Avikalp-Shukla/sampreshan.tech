# 🙏 Sampreshan.tech — Sanatan Voice Platform

**Sampreshan (संप्रेषण)** ka arth hai — *samvad, sandesh, awaaj*.

Yeh platform **Sanatan Dharma ke anuyayiyon** ke liye ek sauchcha digital manch hai jahan log:

- 🤝 Ek doosre se judein aur vichar-vimarsh karein
- ✊ Samajik, dharmik, sanskritik samasyaon ko uthayein
- 📜 Kisi bhi mudde par **support batcha / signature** jama karein
- 🕉️ Aacharyon, peethon aur sampradayon ke baare mein jagruk ho

> **Yahan kisi bhi prakar ka paisa nahi liya jaata — na donation, na membership fee, na koi aur financial transaction. Yeh ek non-commercial awareness + activism platform hai.**

---

## 🌐 Platform ka Uddeshya (Why this exists)

Bharat sahit poore vishwa mein Sanatan Dharma ke anuyayiyon ke apne-apne guru hain, apni sampradaya hain, apne kshetra hain. Bahut baar unki awaaz daba di jaati hai kyunki unki apni community tak woh baat pahunch nahi paati.

**Sampreshan** isi samasya ka samadhan hai — ek aisa digital manch jahan:

- Vartman mein log ek doosre se jud sakein
- Saath mil kar sakaratmak parivartan la sakein
- Kisi bhi samasya ko personally raise karke platform ke logon tak awaaz pahuncha sakein
- Website par signature / support batcha jama kar sakein (kisi bhi signup ke madhyam se, kisi bhi provider se)

---

## 🕉️ Acharya, Peeth aur Sampradaya ke baare mein

Dharm ka sarvashreshtha stambh **aacharya** hain. **Sri Adi Shankaracharya** dwara sthapit **charam dham** (4 peeeth) ke shreshta aacharyon ke saath-sath anek sampradaya ke aacharya aaj bhi logon ke jeevan mein dharmik samasyaon ka hal uplabdh karate hain.

Lekin sabhi shreshta aacharyon ka ek portal par ek saath na hona kahin na kahin logo mein yah aabhas paida karta hai ki *sab mein bhed hai* — jabki dharm ek hai, raahayein anek hain.

Is liye hamara bhaiya platform hai: 👉 **[www.shivbodhtrust.org](https://www.shivbodhtrust.org)** — jaha har aacharya, har peeth aur har sampradaya ka vivechak parichay uplabdh hai.

> **www.sampreshan.tech** ko **Shivbodh Trust** ke netrtva mein banaya gaya hai, keval logo ko technical yug mein ekjut karne ke uddeshya se. Yahan koi paisa nahi liya jaata. Aacharya-parichay aur dharmik margadarshan ke liye **www.shivbodhtrust.org** dekhein.

---

## ✅ Is platform par kya ho sakta hai

| Feature | Description |
|---|---|
| 👤 Member signup & profile | Koi bhi sadasya khud ko register kar sakta hai |
| 💬 Community feed | Vichar, sandesh, post, comment, share |
| ✍️ Issue raise karna | Koi bhi sadasya kisi bhi mudde ko uthata hai |
| 📜 Signature / Support batcha | Kisi bhi provider (Change.org, custom, etc.) ke link se support joda ja sakta hai |
| 🕉️ Acharya awareness section | 4 peeth aur sampradaya ka parichay (link: shivbodhtrust.org) |
| 🌍 Multi-language | Hindi + English (vistar se aur bhashayen jod sakte hain) |

## ❌ Is platform par kya NAHI hota

- ❌ Kisi bhi prakar ka paisa (donation, fee, membership) collect nahi hota
- ❌ Fundraising ya crowdfunding nahi hoti
- ❌ Kisi bhi dharm, sampradaya, jaati ke viruddh koi bhi content nahi
- ❌ Kisi bhi vyakti, sangathan, rajya ke viruddh nindatmak content nahi

---

## 🛠️ Tech Stack (current)

- **Frontend demo:** `demo.html` (single-page static demo, LiteSpeed ke peeche se serve hota hai)
- **CMS:** WordPress + BuddyBoss Platform (members, profiles, groups, messaging, activity, notifications)
- **Page builder:** Elementor / Elementor Pro
- **Caching:** LiteSpeed Cache plugin
- **SEO:** Rank Math + Rank Math Pro
- **Auth add-on:** OneAll Social Login
- **Custom code snippets:** WPCode Premium
- **DB:** MariaDB 10.11 (`samp_sampreshan_wp` — see `db-20260901-185918.sql` for full schema)
- **Web server:** LiteSpeed (Apache `.htaccess` compatible)

Active themes:
- `public_html/wp-content/themes/buddyboss-theme` (primary)
- `public_html/wp-content/themes/hello-elementor` (fallback / Elementor canvas)

---

## 📂 Repository layout

```
sampreshan-tech/
├── demo.html                       # Static landing page (Hindi + English mix)
├── README.md                       # This file
├── .gitignore
├── .htaccess.live                  # Live htaccess (LSCache + WP rewrite + demo nocache)
├── extract.sh                      # Backup archive extraction script
├── db-20260901-185918.sql          # MariaDB dump (full schema + data)
├── files-20260901-185918.tar.gz    # Files backup archive
├── sampreshan-full-20260901-185918.tar.gz
└── public_html/                    # WordPress document root
    ├── .htaccess
    ├── index.php
    ├── wp-config.php
    ├── wp-admin/
    ├── wp-includes/
    └── wp-content/
        ├── themes/                 # buddyboss-theme, hello-elementor
        ├── plugins/                # buddyboss, elementor, litespeed-cache, rank-math, …
        ├── mu-plugins/
        ├── uploads/
        ├── litespeed/              # cache (gitignored)
        └── upgrade/
```

---

## 🚀 Local / dev quickstart

1. Clone: `git clone git@github.com:Avikalp-Shukla/sampreshan.tech.git`
2. Set up local WP + MariaDB (or use the SQL dump to seed).
3. Place contents of `public_html/` in your web root (e.g. MAMP/XAMPP/LiteSpeed local).
4. Import `db-20260901-185918.sql` into your local DB.
5. Update `public_html/wp-config.php` with local DB credentials.
6. For the static demo, just open `demo.html` in a browser.

---

## 🤝 Related projects

- **www.shivbodhtrust.org** — Aacharya/Peeth/Sampradaya parichay portal (sister project by the same trust).

---

## 📜 License & content policy

Yeh platform **non-commercial** hai. Kisi bhi prakar ki financial transaction is site par nahi ki jaati. Content purely awareness, vichar-vimarsh aur samasya-jagrukta ke liye hai. Kisi bhi vyakti, sampradaya, samaj ya dharm ke viruddh apratyashi content ki anumati nahi hai.

🙏 **Dharm ek hai, raahayein anek hain. Sab ka sammaan, sab ka swagat.**
