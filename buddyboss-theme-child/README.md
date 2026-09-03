# SampreShan Child Theme

Custom child theme for **Sampreshan.tech** (Sanatan Voice Platform by ShivBodh Trust).

Built on top of the **BuddyBoss Theme**. Inherits all parent theme functionality — all customizations live here so the parent theme can update safely.

## Structure

\`\`\`
buddyboss-theme-child/
├── style.css          # Theme header + custom CSS
├── functions.php      # Enqueue parent + child setup
├── screenshot.png     # Theme preview (from existing logo)
└── README.md          # This file
\`\`\`

## How it works

- **style.css** declares this as a child of `buddyboss-theme` (Template header)
- **functions.php** enqueues the parent stylesheet FIRST, then child stylesheet (so child CSS wins)
- Any custom template, partial, or override goes here:
  - `template-parts/...` to override parent partials
  - `inc/...` for additional PHP includes
  - `assets/css/...` and `assets/js/...` for enqueued assets

## Development

Edit files here, not in the parent theme. The parent theme can be updated without losing changes.

## Related

- Parent: [BuddyBoss Theme](https://www.buddyboss.com/)
- Site: [sampreshan.tech](https://sampreshan.tech)
- Trust: [shivbodhtrust.org](https://shivbodhtrust.org)
