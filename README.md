<div align="center">

# Wordfence API

[![Packagist Version](https://img.shields.io/packagist/v/typisttech/wordfence-api)](https://packagist.org/packages/typisttech/wordfence-api)
[![PHP Version Require](https://img.shields.io/packagist/dependency-v/typisttech/wordfence-api/php)](https://github.com/typisttech/wordfence-api/blob/main/composer.json#:~:text=%22php%22%3A,%22%2C)
[![Test](https://github.com/typisttech/wordfence-api/actions/workflows/test.yml/badge.svg)](https://github.com/typisttech/wordfence-api/actions/workflows/test.yml)
[![codecov](https://codecov.io/gh/typisttech/wordfence-api/graph/badge.svg?token=PGHZ7ZORC1)](https://codecov.io/gh/typisttech/wordfence-api)
[![License](https://img.shields.io/github/license/typisttech/wordfence-api.svg)](https://github.com/typisttech/wordfence-api/blob/master/LICENSE)
[![Follow @TangRufus on X](https://img.shields.io/badge/Follow-TangRufus-15202B?logo=x&logoColor=white)](https://x.com/tangrufus)
[![Follow @TangRufus.com on Bluesky](https://img.shields.io/badge/Bluesky-TangRufus.com-blue?logo=bluesky)](https://bsky.app/profile/tangrufus.com)
[![Sponsor @TangRufus via GitHub](https://img.shields.io/badge/Sponsor-TangRufus-EA4AAA?logo=githubsponsors)](https://github.com/sponsors/tangrufus)
[![Hire Typist Tech](https://img.shields.io/badge/Hire-Typist%20Tech-778899)](https://typist.tech/contact/)

<p>
  <strong>Fetch WordPress vulnerability information from <a href="https://www.wordfence.com/help/wordfence-intelligence/v2-accessing-and-consuming-the-vulnerability-data-feed/">Wordfence vulnerability data feed.</a></strong>
  <br>
  <br>
  Built with ♥ by <a href="https://typist.tech/">Typist Tech</a>
</p>

</div>

---

> [!TIP]
> **Hire Tang Rufus!**
>
> I am looking for my next role, freelance or full-time.
> If you find this package useful, I can build you more API clients like this.
> Let's talk if you are hiring PHP / Ruby / Go developers.
>
> Contact me at https://typist.tech/contact/

---

## Usage

```php
use \TypistTech\WordfenceAPI\{Client, Feed, Record};

$client = new Client;

// Alternatively, use `Feed::Scanner` for the scanner feed.
$records = $client->fetch(Feed::Production);

foreach($records as $record) {
    /** @var Record $record */
    echo $record->title;
}
```

## Known Issues

### `Allowed memory size of 999999 bytes exhausted (tried to allocate 99 bytes)`

> [!TIP]
> Set `memory_limit` on the fly as a temporary fix:
>
> ```sh
> php -d memory_limit=512MB your-script.php
> ```

As of October 2025, the [production Wordfence vulnerability data feed](https://www.wordfence.com/api/intelligence/v2/vulnerabilities/production) is over 117 MB.
[`Client::fetch()`](./src/Client.php) downloads the feed into memory and `json_decode()` the entire feed all in one go.
It causes PHP to run out of memory.

A possible solution is to use a streaming JSON parser like [`json.Decoder`](https://pkg.go.dev/encoding/json#example-Decoder.Decode-Stream) in Go.
If you know how to do that in PHP, please send pull requests. :bow:

> [!TIP]
> **Hire Tang Rufus!**
>
> There is no need to understand any of these quirks.
> Let me handle them for you.
> I am seeking my next job, freelance or full-time.
>
> If you are hiring PHP / Ruby / Go developers,
> contact me at https://typist.tech/contact/

## Installation

```sh
composer require typisttech/wordfence-api
```

## Credits

[`Wordfence API`](https://github.com/typisttech/wordfence-api) is a [Typist Tech](https://typist.tech) project and maintained by [Tang Rufus](https://x.com/TangRufus), freelance developer [for hire](https://typist.tech/contact/).

Full list of contributors can be found [on GitHub](https://github.com/typisttech/wordfence-api/graphs/contributors).

## Copyright and License

This project is a [free software](https://www.gnu.org/philosophy/free-sw.en.html) distributed under the terms of the MIT license.
For the full license, see [LICENSE](./LICENSE).

### Wordfence Intelligence Terms and Conditions

Before using Wordfence Vulnerability Data Feed API, you must read and agree to the [Wordfence Intelligence Terms and Conditions](https://www.wordfence.com/wordfence-intelligence-terms-and-conditions/).

Learn more at [Wordfence help documentation](https://www.wordfence.com/help/wordfence-intelligence/v2-accessing-and-consuming-the-vulnerability-data-feed/#vulnerability-data-feed).

If you have any questions about the terms and conditions, please contact Wordfence directly.

### MITRE Attribution Requirement

Any company or individual who uses Wordfence vulnerability database API needs to display the MITRE copyright claims included in that vulnerability record for any MITRE vulnerabilities that they display to their end user.

Learn more at [Wordfence help documentation](https://www.wordfence.com/help/wordfence-intelligence/v2-accessing-and-consuming-the-vulnerability-data-feed/#mitre_attribution_requirement).

If you have any questions about the attribution requirement, please contact Wordfence directly.

## Contribute

Feedbacks / bug reports / pull requests are welcome.
