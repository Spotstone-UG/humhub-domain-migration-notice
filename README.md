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

## Removal

Disabling or uninstalling the module invokes HumHub's module cleanup. The
down migration drops the module table; HumHub also removes the module's global
and user-scoped settings. This includes account-level display history.

## Attribution

Created by [Ingo Fleckenstein](https://github.com/ingofleckenstein) with
[Spotstone UG](https://github.com/Spotstone-UG), with assistance from GPT-5.6
Terra. The module itself remains generic and contains no deployment-specific
domain or organisation configuration.

## Licence

No licence has been selected yet. Select and add a licence before publishing
or distributing the module.
