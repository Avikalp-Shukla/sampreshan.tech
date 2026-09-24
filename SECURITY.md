# Security Policy

Sampreshan और Shivbodh Trust community की सुरक्षा हमारी प्राथमिकता है। यह repository public है; इसलिए credentials, personal data और production infrastructure details की सुरक्षा विशेष रूप से आवश्यक है।

## Vulnerability report करना

किसी संभावित security vulnerability को public issue, discussion या pull request में पोस्ट न करें। Private report भेजें:

- Email: [sampreshan.tech@gmail.com](mailto:sampreshan.tech@gmail.com)
- Subject में `Security report` लिखें।

Report में, यदि सुरक्षित हो, निम्न जानकारी दें:

- प्रभावित URL, file, component या version
- Vulnerability का impact
- Reproduction steps या proof of concept
- संभावित remediation
- क्या कोई credential, personal data या production system प्रभावित हुआ है

कृपया credentials, live tokens, private keys, database dumps या personal data email में शामिल न करें। यदि exposure हो चुका है, पहले credential rotate/revoke करें और फिर report करें।

## अपेक्षित response

Maintainers report को acknowledge करने, impact समझने और उचित remediation तय करने का प्रयास करेंगे। Response time incident की severity, उपलब्ध information और maintainer capacity पर निर्भर हो सकता है।

## Repository security rules

- `.env`, `wp-config.php`, API keys, passwords, tokens और private keys commit न करें।
- Production database dumps, backups, uploads और personal data source control से बाहर रखें।
- GitHub access में least privilege अपनाएँ।
- `main` में direct push के बजाय reviewed pull request का उपयोग करें।
- Suspected exposure पर credentials तुरंत rotate करें और repository history की समीक्षा करें।
- Dependencies, WordPress core, themes और plugins को supported तथा patched versions पर रखें।
- Security-sensitive changes को local testing और review के बिना deploy न करें।

## Scope और responsible disclosure

यह policy repository, Sampreshan website और project-owned integrations से संबंधित reports पर लागू होती है। Social engineering, denial-of-service, spam, physical attacks और third-party systems को target करने वाले tests scope में नहीं हैं।

Good-faith testing करें, user data access न करें, और report को public करने से पहले maintainers को उचित समय दें।
