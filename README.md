# 🙏 Sampreshan.tech

**Sampreshan (संप्रेषण)** — संवाद, जागरूकता और रचनात्मक सामुदायिक सहभागिता का एक open-source digital platform.

> **Shivbodh Trust की महत्वाकांक्षी सामाजिक-तकनीकी परियोजना**
>
> Sanatana Dharma से जुड़े लोगों, समुदायों और संस्थाओं को सम्मानजनक संवाद, जागरूकता, समस्याओं को सामने रखने और सकारात्मक, शांतिपूर्ण तथा रचनात्मक सहभागिता के लिए एक विश्वसनीय मंच उपलब्ध कराना।

[![Website](https://img.shields.io/badge/website-sampreshan.tech-0f766e?style=flat-square&logo=google-chrome&logoColor=white)](https://sampreshan.tech)
[![Open Source](https://img.shields.io/badge/project-open%20source-2563eb?style=flat-square&logo=github)](https://github.com/Sampreshan/sampreshan.tech)
[![Maintained by Shivbodh Trust](https://img.shields.io/badge/maintained%20by-Shivbodh%20Trust-f59e0b?style=flat-square)](https://shivbodhtrust.org)

## परियोजना के बारे में

Sampreshan एक **open-source और non-commercial** पहल है। इसका उद्देश्य technology का उपयोग करके लोगों और समुदायों को जोड़ना, authentic information और awareness को बढ़ावा देना, तथा lawful और constructive community participation को आसान बनाना है।

यह परियोजना **Shivbodh Trust की दीर्घकालीन और महत्वाकांक्षी vision** से प्रेरित है। यह किसी एक व्यक्ति, क्षेत्र या संप्रदाय तक सीमित नहीं है; इसका लक्ष्य Sanatana Dharma से जुड़े विविध समुदायों के बीच सम्मानजनक संवाद और सहयोग को बढ़ावा देना है।

### हमारा vision

- लोगों और समुदायों के बीच respectful communication को बढ़ावा देना।
- सांस्कृतिक और धार्मिक heritage, शिक्षा तथा परंपराओं के बारे में awareness बढ़ाना।
- सामाजिक और सामुदायिक मुद्दों को शांतिपूर्ण, lawful और constructive तरीके से उठाने का मंच देना।
- technology को accessible, secure, maintainable और उपयोगी बनाना।
- दुनिया भर के contributors को open-source collaboration का अवसर देना।

## मुख्य क्षमताएँ

| क्षेत्र | उद्देश्य |
| --- | --- |
| Community profiles और activity | लोगों को connect, communicate और collaborate करने में सहायता |
| Issues और awareness | महत्वपूर्ण विषयों को structured तरीके से सामने रखना |
| Support और participation | community support तथा constructive action को संगठित करना |
| Cultural और religious awareness | Acharya, Peeth, Sampradaya और heritage से संबंधित जानकारी साझा करना |
| बहुभाषी अनुभव | Hindi, English और भविष्य की अन्य भाषाओं के लिए आधार |
| Responsible moderation | सम्मानजनक, authentic और सुरक्षित community environment |

## क्या Sampreshan नहीं है

- यह fundraising, donation या crowdfunding platform नहीं है।
- यह किसी व्यक्ति, धर्म, संप्रदाय, जाति या समुदाय के विरुद्ध घृणा, अपमान या हिंसा का मंच नहीं है।
- यह defamation, harassment, personal attacks, illegal activities या social conflict को बढ़ावा नहीं देता।
- Repository में मौजूद code और documentation को production-ready मानने से पहले review और testing आवश्यक है।

## Open-source community

हम responsible collaboration का स्वागत करते हैं। आप निम्न प्रकार से योगदान कर सकते हैं:

- Bug report या feature request के लिए [Issue खोलें](https://github.com/Sampreshan/sampreshan.tech/issues)।
- Documentation, accessibility, testing और code quality में सुधार करें।
- छोटे और focused pull requests भेजें।
- Security समस्या को public issue में पोस्ट न करें; [Security Policy](SECURITY.md) देखें।

योगदान शुरू करने से पहले [CONTRIBUTING.md](CONTRIBUTING.md), [CODE_OF_CONDUCT.md](CODE_OF_CONDUCT.md) और [DEVELOPMENT.md](DEVELOPMENT.md) पढ़ें।

## Technology stack

- **Application:** WordPress और BuddyBoss Platform
- **Themes/UI:** BuddyBoss Theme, child theme और Elementor
- **Web server:** LiteSpeed / Apache-compatible configuration
- **Database:** MariaDB
- **Caching और SEO:** LiteSpeed Cache तथा Rank Math
- **Automation:** GitHub Actions, shell scripts और project-specific tooling
- **Optional design integration:** Framer sync tooling

Technology choices project की जरूरतों के अनुसार बदल सकती हैं।

## Repository structure

```text
.
├── README.md
├── CONTRIBUTING.md
├── CODE_OF_CONDUCT.md
├── SECURITY.md
├── DEVELOPMENT.md
├── DEPLOYMENT-RUNBOOK.md
├── .github/                 # issue और pull request templates
├── buddyboss-theme-child/   # Sampreshan के custom theme changes
├── framer-sync/             # Framer से संबंधित optional tooling
├── public_html/             # WordPress document root
└── demo.html                # Static/demo experience
```

### संवेदनशील files के बारे में महत्वपूर्ण सूचना

Production configuration, credentials, `.env` files, database exports, backups और user uploads को source control में नहीं रखना चाहिए। Existing snapshots या exports को production secrets की तरह treat करें: उनकी समीक्षा करें, आवश्यकता न हो तो हटाएँ, और यदि credentials कभी commit हुए हों तो उन्हें rotate करें। पूरी guidance के लिए [SECURITY.md](SECURITY.md) देखें।

## Local development — संक्षिप्त रूपरेखा

1. Repository clone करें:

   ```bash
   git clone https://github.com/Sampreshan/sampreshan.tech.git
   cd sampreshan.tech
   ```

2. Local WordPress और MariaDB environment तैयार करें।
3. `public_html/` को अपने local web root में रखें।
4. केवल सुरक्षित और scrubbed development database snapshot का उपयोग करें।
5. Local configuration values को environment variables या ignored local config में रखें; production credentials का उपयोग न करें।
6. WordPress, theme और plugin changes को local environment में test करें।
7. Pull request खोलने से पहले relevant checks, security review और documentation validation करें।

Detailed setup और development guidance: [DEVELOPMENT.md](DEVELOPMENT.md).

## Project governance

- `main` को stable और production-ready रखने का प्रयास किया जाता है।
- Production-impacting बदलाव pull request और review के माध्यम से होने चाहिए।
- Security और privacy concerns को responsibly disclose किया जाना चाहिए।
- AI-assisted contributions को merge से पहले human review, testing और security validation से गुजरना होगा।
- Repository access trusted maintainers तक सीमित रखा जाना चाहिए।

Repository की GitHub settings में branch protection, required reviews, required checks, conversation resolution और restricted pushes enabled होने चाहिए।

## संबंधित परियोजनाएँ

- [Shivbodh Trust](https://shivbodhtrust.org) — Acharya, Peeth और Sampradaya awareness initiative
- [Sampreshan website](https://sampreshan.tech)

## License और content policy

इस repository के software, documentation, branding और content पर लागू license और usage terms को project maintainers द्वारा स्पष्ट और अद्यतन रखा जाएगा। जब तक किसी file या repository section में अलग license न हो, कोई भी copyright या trademark अधिकार स्वतः प्रदान नहीं माना जाना चाहिए।

Sampreshan एक non-commercial awareness और community-participation initiative है। Platform पर lawful, peaceful, respectful और constructive engagement अपेक्षित है।

## संपर्क

- **General platform communication:** [sampreshan.tech@gmail.com](mailto:sampreshan.tech@gmail.com)
- **Security reports:** [SECURITY.md](SECURITY.md)
- **Website:** [sampreshan.tech](https://sampreshan.tech)

🙏 **धर्म एक है, मार्ग अनेक हैं — सम्मान, संवाद और सहयोग के साथ।**
