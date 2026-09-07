<?php
/**
 * Home: Flag Cinema — waving Sanatan dhwaja, full-width (full-bleed).
 * Hindi "संप्रेषण" then English "SAMPRESHAN" cycle continuously,
 * like video captions. Pure CSS motion, no video files.
 *
 * @package SampreShan_Child
 */
?>
<section class="sp-flagcinema sp-fullbleed" aria-label="<?php esc_attr_e( 'Sanatan Dharma flag with Sampreshan', 'sampreshan-child' ); ?>">
    <div class="sp-flagcinema__sun" aria-hidden="true"></div>
    <span class="sp-flagcinema__mote sp-flagcinema__mote--1" aria-hidden="true"></span>
    <span class="sp-flagcinema__mote sp-flagcinema__mote--2" aria-hidden="true"></span>
    <span class="sp-flagcinema__mote sp-flagcinema__mote--3" aria-hidden="true"></span>
    <span class="sp-flagcinema__mote sp-flagcinema__mote--4" aria-hidden="true"></span>
    <div class="sp-flagcinema__inner">
        <svg class="sp-flagcinema__flag" viewBox="0 0 400 260" role="img" aria-label="<?php esc_attr_e( 'Waving saffron flag of Sanatan Dharma', 'sampreshan-child' ); ?>">
            <defs>
                <linearGradient id="spfPole" x1="0" y1="0" x2="1" y2="0">
                    <stop offset="0%" stop-color="#8A6D1F"/><stop offset="50%" stop-color="#F5D98A"/><stop offset="100%" stop-color="#8A6D1F"/>
                </linearGradient>
                <linearGradient id="spfCloth" x1="0" y1="0" x2="0" y2="1">
                    <stop offset="0%" stop-color="#FFB877"/><stop offset="45%" stop-color="#FF9933"/><stop offset="100%" stop-color="#C2410C"/>
                </linearGradient>
                <linearGradient id="spfClothBack" x1="0" y1="0" x2="0" y2="1">
                    <stop offset="0%" stop-color="#E0851A"/><stop offset="100%" stop-color="#7A1F1E"/>
                </linearGradient>
                <clipPath id="spfClip">
                    <path d="M53 34 C120 14, 180 54, 250 34 C300 20, 340 34, 372 28 L372 148 C340 154, 300 140, 250 154 C180 170, 120 132, 53 152 Z"/>
                </clipPath>
            </defs>
            <ellipse cx="48" cy="252" rx="34" ry="5" fill="#000000" opacity="0.45"/>
            <rect x="38" y="238" width="20" height="8" rx="2" fill="url(#spfPole)"/>
            <rect x="43" y="14" width="10" height="228" rx="4" fill="url(#spfPole)"/>
            <circle cx="48" cy="10" r="8" fill="url(#spfPole)"/>
            <circle cx="48" cy="10" r="3" fill="#FFF3DC"/>
            <g class="sp-flagcinema__cloth sp-flagcinema__cloth--back" aria-hidden="true">
                <path d="M53 40 C120 20, 180 60, 250 40 C300 26, 340 40, 372 34 L372 154 C340 160, 300 146, 250 160 C180 176, 120 138, 53 158 Z" fill="url(#spfClothBack)"/>
            </g>
            <g class="sp-flagcinema__cloth" aria-hidden="true">
                <path d="M53 34 C120 14, 180 54, 250 34 C300 20, 340 34, 372 28 L372 148 C340 154, 300 140, 250 154 C180 170, 120 132, 53 152 Z" fill="url(#spfCloth)"/>
                <rect class="sp-flagcinema__shine" x="-40" y="0" width="70" height="200" fill="#FFFFFF" opacity="0.16" clip-path="url(#spfClip)" transform="skewX(-12)"/>
                <text x="212" y="112" text-anchor="middle" font-size="78" font-family="Georgia, 'Noto Serif Devanagari', serif" font-weight="bold" fill="#FFF8EC" stroke="#7A1F1E" stroke-width="2" style="paint-order:stroke">ॐ</text>
            </g>
        </svg>
        <div class="sp-flagcinema__words" aria-hidden="true">
            <span class="w-hi">संप्रेषण</span>
            <span class="w-en">SAMPRESHAN</span>
        </div>
        <p class="sp-flagcinema__tag"><?php esc_html_e( 'One flag. One voice. Sanatan Dharma.', 'sampreshan-child' ); ?></p>
    </div>
</section>
