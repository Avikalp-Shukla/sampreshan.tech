# Dharma Acharya and Peeth Directory

Sampreshan provides a public directory for Dharma Acharyas and Peeths. It is
implemented in the BuddyBoss child theme and is independent of the legacy
ShivBodh Trust pages.

## What is included

- Five seeded Peeth profiles: Govardhan Math, Puri; Dwarka Sharada Peetham;
  Jyotirmath; Sringeri Sharada Peetham; and Kanchi Kamakoti Peetham.
- Five seeded Acharya profiles connected with their respective Peeth
  traditions.
- When the existing migrated legacy pages are present, their detailed body
  content is copied into a new profile once, with legacy domain and brand
  references replaced by Sampreshan equivalents.
- Search and Peeth filtering at `/dharma-acharya/`.
- Individual public profiles at `/dharmacharya/<profile-slug>/`.
- An **Anusaran** button for signed-in members.
- A dashboard section containing the profiles a member follows and their
  latest verified updates.
- BuddyBoss notifications whenever a verified update is published for a
  followed profile.
- Homepage discovery cards.
- Profile images resolve from each profile's configured Media Library filename,
  so an editor can upload the prepared image under that filename without
  editing a hard-coded attachment ID.

The old profile and Peeth page URLs are permanently redirected to their
Sampreshan directory equivalents. New pages use Sampreshan metadata and
structured data; no legacy-site organization identity is added by this module.

## Editorial workflow

1. In WordPress admin, open **Dharma Profiles** to edit the seeded profile
   information or add another Acharya profile.
2. Set the profile's **Peeth** taxonomy and complete its summary/content.
3. Open **Dharma Updates** and create an update for a verified programme,
   activity, or news item.
4. In the **Associated Acharya or Peeth profile** panel, choose the profile.
5. Publish only after editorial verification. Publishing sends an in-platform
   notification to members who selected Anusaran for that profile.

Members can use **Raise a connected issue** from a profile to start an Issue
Sampreshan with that profile preselected as the petition target. This records
a public community concern; it does not represent a direct official message
channel to an Acharya or Peeth.

## Safety and maintenance

- Do not publish unverified claims, schedules, statements, or contact details.
- Do not edit `_sp_dharma_*` custom fields directly unless changing the
  directory implementation.
- The first live request after deployment seeds missing standard profiles and
  refreshes WordPress rewrite rules once. Existing profile records are never
  overwritten by the seeder, so edit their migrated content safely afterward.
