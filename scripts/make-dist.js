import { rm, mkdir, copyFile, readdir } from "fs/promises";
import path from "path";

async function copyDir(src, dest) {
    await mkdir(dest, { recursive: true });
    const entries = await readdir(src, { withFileTypes: true });
    for (const entry of entries) {
        const srcPath = path.join(src, entry.name);
        const destPath = path.join(dest, entry.name);
        if (entry.isDirectory()) {
            await copyDir(srcPath, destPath);
        } else if (entry.isFile()) {
            await copyFile(srcPath, destPath);
        }
    }
}

(async () => {
    try {
        const src = path.resolve("public");
        const dest = path.resolve("dist");
        // remove existing dist if any
        await rm(dest, { recursive: true, force: true });
        // copy public -> dist
        await copyDir(src, dest);
        console.log("Copied public -> dist");
    } catch (err) {
        console.error("Failed to create dist from public:", err);
        process.exit(2);
    }
})();
