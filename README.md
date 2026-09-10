# Domain Migration Notice

A HumHub Community Edition 1.18.5 module that helps an administrator guide
people from an old domain or subdomain to a configured destination URL.

It compares hosts only. `http` and `https` are intentionally treated as the
same host, while `community.example.org` and `example.org` are distinct.

## App description

Domain Migration Notice helps communities communicate a domain move with care
and clarity. Instead of silently redirecting people, it presents a configurable
message, countdown and direct link to the new address. The message becomes more
frequent as the deadline approaches. After the deadline, the old community is
replaced by a migration-only page so that nobody continues working at the wrong
address.

Administrators can tailor all visible wording, enable an optional one-week
dismissal, explain the necessary guest cookie, add scoped custom CSS and check
the result with a preview before activating the notice.

## Safety by design

- The rule is inactive until it has a valid HTTP(S) target URL, a deadline and
  an explicit enablement.
- Enabling the notice is refused if its deadline is already in the past or if
  the configured target host is the host currently being configured. Both
  mistakes would otherwise make a migration notice ineffective or immediately
  lock the community.
- The post-deadline block keeps the module's migration page and sign-in route
  reachable so a system administrator can correct or disable a mistaken rule.
- Only CSRF-protected, same-origin POST requests store display state. The
  destination URL cannot contain credentials, and the rich-text field uses
  HumHub's configured rich-text renderer.

## Behaviour

The notice is displayed only when the module is enabled, configured, and the
current host is not the destination host.

| Time remaining until the deadline | Maximum display frequency |
| --- | --- |
| More than 7 days | Once per day; an optional one-week dismissal is offered |
| From 7 days to more than 3 days | Once per hour |
| The final 3 days | Every page load |
| Deadline reached | Community content is replaced by a migration-only page |

Signed-in people have their frequency state stored with their HumHub account.
Guests can optionally dismiss the notice for one week through a browser cookie.
The administrator can show or hide the cookie notice independently.

The countdown label, its format (`{days}`, `{hours}`, `{minutes}`) and its
post-deadline text are configurable. This keeps every visitor-facing text under
administrator control.

## Installation

1. Copy this directory to `protected/modules/domainmigrationnotice` in a
   HumHub 1.18.5 installation.
2. Open **Administration → Modules**, find **Domain Migration Notice**, and
   enable it. HumHub runs the module migration automatically.
3. Open the module configuration page and set a valid destination URL and
   deadline before enabling the notice.
4. Use **Preview notice** to check the message without relying on an old
   domain.

The administrator configuration uses HumHub's rich-text editor, which stores
and safely renders Markdown-compatible content according to the instance's
configured rich-text implementation.

### Before enabling

Test the notice in a non-production environment first. In particular, verify
the destination link, deadline timezone, desktop and mobile layout, the guest
cookie text, account-based frequency behaviour, and the blocked page. Make a
database backup before enabling any module in a production community.

Custom CSS has the same trust boundary as theme code: only administrators who
are allowed to change the community's presentation should use it. The module
rejects angle brackets so a style field cannot close its generated style block.

## Removal

Disabling or uninstalling the module invokes `migrations/uninstall.php`, which
drops the module configuration table. HumHub's module cleanup also removes its
global and user-scoped settings. This includes account-level display history.
Guest cookies are stored in visitors' browsers and cannot be removed remotely;
they expire automatically after their configured daily, hourly, or one-week
period.

## Development checks

Run the isolated checks with:

```sh
php tests/run.php
```

They cover host comparison, subdomain handling, IPv6 normalisation and the
three deadline thresholds. For a release, additionally enable, configure,
update and disable the module in a HumHub 1.18.5 test installation.

## Attribution

Created by [Ingo Fleckenstein](https://github.com/ingofleckenstein) with
[Spotstone UG](https://github.com/Spotstone-UG), with assistance from GPT-5.6
Terra. The module itself remains generic and contains no deployment-specific
domain or organisation configuration.

## Licence

No licence has been selected yet. Select and add a licence before publishing
or distributing the module. Because this repository is public, its source is
currently protected by the default copyright position and is not yet offered
for reuse under an open-source licence.
