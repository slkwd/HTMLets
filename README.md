# HTMLets

**HTMLets** is a modernized MediaWiki extension that lets you include (inline) static HTML snippets into wiki pages using the `<htmlet>` tag.

- **Status:** Maintained (modern extension.json version, compatible with MediaWiki 1.31+ and 1.43+)
- **Type:** Tag extension (`<htmlet>`)
- **Author:** Daniel Kinzler (original), modernized by the community
- **License:** GPL-2.0-or-later
- **Hooks used:** `ParserFirstCallInit`, `ParserAfterTidy`
- **Configuration:** `$wgHTMLetsDirectory`
- **Tags:** `<htmlet>`
- **MediaWiki compatibility:** 1.31+, 1.43+ (tested)

---

## Description

The HTMLets extension provides a way to include (inline) static HTML snippets into wiki pages. This is useful for safely embedding special HTML, JavaScript, iframes, or forms, without enabling arbitrary raw HTML for all users.

---

## Installation

1. Download or clone this repository into your MediaWiki `extensions/HTMLets` directory.
2. Ensure the directory structure includes:
   - `extension.json`
   - `includes/Hooks.php`
   - `includes/TagHandler.php`
   - `i18n/` (with translation files)
3. Add the following to your `LocalSettings.php`:
   ```php
   wfLoadExtension( 'HTMLets' );
   // Optional: set a custom directory for HTML snippets
   $wgHTMLetsDirectory = "$IP/extensions/HTMLets";
   ```
   If you do not set `$wgHTMLetsDirectory`, it defaults to `$IP/htmlets`.

---

## Usage

To include an HTML snippet in a wiki page, use the `<htmlet>` tag:

```html
<htmlet>foobar</htmlet>
```

This will include the contents of the file `foobar.html` from the configured HTMLets directory.

If the snippet changes often and you want those changes reflected immediately, you can disable the parser cache for the page:

```html
<htmlet nocache="yes">foobar</htmlet>
```

### Directory Configuration

- The directory for HTML snippets is set with `$wgHTMLetsDirectory` in `LocalSettings.php`.
- By default, it is `$IP/htmlets`.
- You may also set it to a URL (e.g., `http://localhost/htmlets/`), but the `.html` extension is always enforced.

---

## Parser Issues and Hack Modes

Due to MediaWiki parser limitations, HTMLets provides "hack modes" to avoid mangling of HTML content:

- **bypass** (default): Encodes the HTML as Base64 and decodes it after the parser's tidy phase.
- **strip**: Normalizes whitespace and encodes problematic characters. May break `<pre>` and some JavaScript.
- **none**: No hack; content is passed directly to the parser (may be mangled).

Set the hack mode globally with `$wgHTMLetsHack` or per-tag with the `hack` attribute:

```html
<htmlet hack="strip">foobar</htmlet>
```

---

## See Also

- [HTML restriction](https://www.mediawiki.org/wiki/HTML_restriction)
- [Extension:HTML Tags](https://www.mediawiki.org/wiki/Extension:HTML_Tags)
- [Extension:CloudImage](https://www.mediawiki.org/wiki/Extension:CloudImage)
- [Extension:Widgets](https://www.mediawiki.org/wiki/Extension:Widgets)

---

## License

GPL-2.0-or-later

---

## Credits

- Original author: Daniel Kinzler (Duesentrieb)
- Modernization: Community contributors