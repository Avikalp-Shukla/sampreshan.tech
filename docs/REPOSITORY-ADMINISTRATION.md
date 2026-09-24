# Repository administration and access checklist

यह guide `Sampreshan/sampreshan.tech` के repository owners और trusted maintainers के लिए है। इसका उद्देश्य GitHub settings को सुरक्षित, reviewable और repeatable तरीके से configure करना है।

> **महत्वपूर्ण:** यह file configuration policy और verification steps देती है। GitHub repository settings को लागू करने के लिए repository admin को GitHub UI में नीचे दिए steps पूरे करने होंगे। कोई भी setting बदलने के बाद इस checklist को दोबारा चलाएँ।

## 1. Repository identity और About

Repository: `Sampreshan/sampreshan.tech`

`Settings → General` में verify करें:

- **Repository name:** `sampreshan.tech`
- **Default branch:** `main`
- **Visibility:** `Public` — यदि open-source collaboration जारी रखना है
- **Description:** Sampreshan — digital platform for Sanatan Dharma followers worldwide to connect, raise issues, and create positive change. By Shivbodh Trust.
- **Website / Homepage:** `https://sampreshan.tech`
- **Topics:**
  - `sanatan`
  - `community`
  - `wordpress`
  - `buddyboss`
  - `open-source`
  - `shivbodh-trust`

Repository home page के **About → gear icon** में Website और Topics भी verify करें। Website field और homepage URL को एक ही canonical HTTPS address पर रखें।

## 2. Features

`Settings → General → Features` में:

- [ ] Issues — ON
- [ ] Discussions — ON
- [ ] Projects — ON
- [ ] Wiki — आवश्यकता न हो तो OFF करने पर विचार करें
- [ ] Sponsorships — केवल वास्तविक sponsorship program होने पर ON

Discussions में categories कम-से-कम इस प्रकार रखें:

- Announcements
- General
- Ideas
- Q&A
- Show and tell

Security vulnerability को Discussions या Issues में public रूप से पोस्ट नहीं किया जाना चाहिए। इसके लिए [SECURITY.md](../SECURITY.md) प्रक्रिया का उपयोग करें।

## 3. Collaborator और team permission model

`Settings → Collaborators and teams → Manage access` में least privilege लागू करें:

| Role | किसे दें | क्या अनुमति है |
| --- | --- | --- |
| Admin | केवल repository owner और अत्यंत trusted backup administrator | Settings, access, rules और destructive actions |
| Maintain | Release/deployment maintainers | Repository maintenance और releases; settings ownership नहीं |
| Write | Active trusted developers | Branches/PRs पर development work |
| Triage | Issue/PR moderators | Issues और PRs manage करना; code push नहीं |
| Read | External observers या आवश्यक read-only integrations | Code और public project देखना |

Safe default:

- [ ] Unknown या inactive collaborators हटाए गए हैं।
- [ ] Admin users की संख्या न्यूनतम है।
- [ ] रोज़मर्रा के development के लिए Admin के बजाय Write/Maintain role उपयोग हो रहा है।
- [ ] Teams को individual users से प्राथमिकता दी गई है, जब organization teams उपलब्ध हों।
- [ ] कोई public user collaborator के रूप में add नहीं किया गया है।
- [ ] Outside collaborators और deploy keys की quarterly review होती है।

Public repository में सामान्य visitors को code पढ़ने और fork करने की अनुमति होती है, लेकिन collaborator access न होने पर उन्हें repository में push, protected branch update या merge की permission नहीं मिलती।

## 4. Branch protection — recommended `main` rule

`Settings → Rules → Rulesets` उपलब्ध हो तो **New branch ruleset** बनाना preferred है। पुराने UI में `Settings → Branches → Add classic branch protection rule` उपयोग करें।

### Rule target

- **Name:** `Protect main`
- **Target:** branch name pattern `main`
- **Enforcement status:** Active

### Required protections

Enable करें:

- [ ] Require a pull request before merging
- [ ] Require at least **1 approval**
- [ ] Dismiss stale pull request approvals when new commits are pushed — recommended
- [ ] Require approval of the most recent reviewable push — recommended
- [ ] Require conversation resolution before merging
- [ ] Require status checks to pass before merging
- [ ] Require branches to be up to date before merging — recommended once checks are reliable
- [ ] Block force pushes
- [ ] Block deletions
- [ ] Restrict who can push to matching branches
- [ ] Do not allow bypassing / Include administrators

### Required status checks

पहले `Actions` में मौजूद workflows को एक बार run होने दें। फिर protection rule के **required status checks** section में केवल stable, relevant checks चुनें। उदाहरण:

- PHP syntax या WordPress validation
- JavaScript lint/test
- Security/dependency scan
- Build या deployment validation

ऐसे checks को required न करें जो अभी मौजूद नहीं हैं, हमेशा pending रहते हैं, या इस repository के लिए relevant नहीं हैं। Check name बदलने पर branch rule को update करें।

### Push restriction

`Restrict who can push to matching branches` में केवल:

- repository owner
- approved release maintainer team
- आवश्यक deployment bot/app

को allow करें। किसी सामान्य contributor को `main` पर direct push permission न दें। Pull request merge permission को भी trusted maintainers तक सीमित रखें।

### Bypass review

- Bypass list खाली रखें, या केवल emergency administrator रखें।
- यदि bypass आवश्यक हो, तो उसका कारण और incident record रखें।
- `Do not allow bypassing the above settings` enabled रखें।

## 5. Pull request और merge settings

`Settings → General → Pull Requests` में project policy के अनुसार:

- [ ] Allow squash merging — ON; default message `Pull request title` या project convention
- [ ] Allow merge commits — आवश्यकता अनुसार; सामान्यतः OFF रखा जा सकता है
- [ ] Allow rebase merging — आवश्यकता अनुसार
- [ ] Automatically delete head branches — ON recommended
- [ ] Allow auto-merge — केवल required checks और review rules के साथ
- [ ] Allow update branch — केवल trusted workflow की आवश्यकता होने पर

Production-impacting बदलाव हमेशा reviewed PR से merge हों।

## 6. Security और repository hygiene

`Settings → Security` तथा `Security` tab में, plan के अनुसार, enable करें:

- [ ] Dependabot alerts
- [ ] Dependabot security updates
- [ ] Secret scanning
- [ ] Push protection, यदि plan में उपलब्ध हो
- [ ] Code scanning, यदि project workflow उपलब्ध और maintainable हो

साथ ही:

- [ ] `.env`, `wp-config.php`, credentials और private keys ignored हैं।
- [ ] Production database dumps, archives, uploads और backups repository में नहीं हैं।
- [ ] यदि कोई secret commit हुआ हो तो केवल file हटाना पर्याप्त नहीं — credential revoke/rotate और history remediation करें।
- [ ] GitHub Actions secrets में केवल आवश्यक secrets हैं और उन्हें logs में print नहीं किया जाता।
- [ ] Actions को minimum permissions (`contents: read` जहाँ संभव हो) से चलाया जाता है।

## 7. Non-collaborator verification

Verification के लिए ऐसा GitHub account चुनें जो collaborator, organization member, team member या repository owner न हो। बेहतर है private/incognito browser और अलग Git credentials उपयोग करें।

### Browser test

- [ ] `https://github.com/Sampreshan/sampreshan.tech` खोलने पर code visible है।
- [ ] README, Issues, Discussions और public Projects expected रूप से दिखते हैं।
- [ ] `Settings`, `Manage access` या repository administration controls दिखाई नहीं देते।
- [ ] `main` पर direct edit/push control उपलब्ध नहीं है।
- [ ] Account को collaborator invite के बिना repository settings या protected branch bypass option नहीं मिलता।
- [ ] Public repository होने के कारण fork और pull request का विकल्प दिख सकता है — यह write access नहीं है।

### Safe Git test

पहले read operation test करें:

```bash
git clone https://github.com/Sampreshan/sampreshan.tech.git
cd sampreshan.tech
git switch --create permission-test
printf '\nPermission test\n' >> /tmp/sampreshan-permission-test.txt
```

Local test file को repository में commit किए बिना हटाएँ:

```bash
rm -f /tmp/sampreshan-permission-test.txt
```

यदि push behavior test करना आवश्यक हो, तो disposable clone और harmless temporary branch उपयोग करें — `main` पर test push न करें:

```bash
git switch --create permission-test
printf 'permission test\n' > permission-test.txt
git add permission-test.txt
git commit -m "test: verify public user cannot push"
git push origin permission-test
```

Expected result: authentication/authorization failure, जैसे `Permission denied` या `Write access to repository not granted`। Test सफल होने के बाद local branch और file हटाएँ। यदि accidental remote branch बन जाए तो trusted maintainer उसे remove करे।

### Test के बाद cleanup

- [ ] Temporary local branch और file हटाई गई।
- [ ] कोई temporary collaborator invite नहीं बचा।
- [ ] कोई test token या credential save नहीं हुआ।
- [ ] `main` branch पर कोई test commit नहीं आया।
- [ ] Result और date को project administration record में दर्ज किया गया।

## 8. Final sign-off

हर settings change के बाद owner/administrator verify करे:

- [ ] Default branch `main` है।
- [ ] Website, description और topics सही हैं।
- [ ] Discussions और Projects enabled हैं।
- [ ] केवल trusted users को Write/Maintain/Admin access है।
- [ ] `main` PR, approval, checks और conversation resolution से protected है।
- [ ] Force push और branch deletion disabled हैं।
- [ ] Bypass disabled या formally documented है।
- [ ] Non-collaborator read-only test सफल है।
- [ ] Security alerts और secret protection enabled हैं, जहाँ उपलब्ध हैं।

**Review cadence:** access list और branch rules को कम-से-कम quarterly तथा हर team/person change के बाद review करें।
