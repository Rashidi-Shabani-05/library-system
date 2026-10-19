# Locally bundled frontend assets

These browser assets are served from this project; pages do not request them from a CDN.

| Package | Version | Files used | Upstream |
| --- | --- | --- | --- |
| AdminLTE | 4.10.0 | `dist/css/adminlte.min.css`, `dist/js/adminlte.min.js` | [ColorlibHQ/AdminLTE](https://github.com/ColorlibHQ/AdminLTE/releases/tag/v4.10.0) |
| Bootstrap | 5.3.8 | `plugins/bootstrap/css/bootstrap.min.css`, `plugins/bootstrap/js/bootstrap.bundle.min.js` | [twbs/bootstrap](https://github.com/twbs/bootstrap) |
| Bootstrap Icons | 1.13.1 | minified stylesheet and WOFF/WOFF2 fonts | [twbs/icons](https://github.com/twbs/icons) |
| OverlayScrollbars | 2.11.0 | minified stylesheet and browser ES module build | [KingSora/OverlayScrollbars](https://github.com/KingSora/OverlayScrollbars) |

The Bootstrap bundle includes Popper. AdminLTE 4 uses vanilla JavaScript/TypeScript and does not need jQuery. License texts for all four projects are in `LICENSES/`. The versions above are pinned for this classroom package; check the upstream release pages before choosing versions for a new project.
