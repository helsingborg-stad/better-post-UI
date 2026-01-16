import { createViteConfig } from "vite-config-factory";

const entries = {
	'js/main': './source/js/main.js', 
	'js/author': './source/js/author.js',
	'js/order': './source/js/order.js',
	'js/parent': './source/js/parent.js',
	'js/publish-actions': './source/js/publish-actions.js',
	'css/better-post-ui': './source/sass/better-post-ui.scss'
}

export default createViteConfig(entries, {
	outDir: "assets/dist",
	manifestFile: "manifest.json",
});
