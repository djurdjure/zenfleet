/**
 * PDF Microservice - Enterprise Grade v2.1
 * Service Node.js pour génération de PDFs haute qualité
 * Compatible avec format ES6 et CommonJS
 */

import express from 'express';
import puppeteer from 'puppeteer';
import cors from 'cors';

const app = express();
const PORT = process.env.PORT || 3000;

// Configuration middleware
app.use(cors());
app.use(express.json({ limit: '50mb' }));
app.use(express.urlencoded({ limit: '50mb', extended: true }));

// Variable pour stocker l'instance du browser
let browser = null;

// Initialisation du browser Puppeteer
async function initBrowser() {
    if (!browser) {
        browser = await puppeteer.launch({
            headless: 'new',
            args: [
                '--no-sandbox',
                '--disable-setuid-sandbox',
                '--disable-dev-shm-usage',
                '--disable-accelerated-2d-canvas',
                '--disable-gpu',
                '--window-size=1920,1080'
            ]
        });
        console.log('✅ Browser Puppeteer initialisé');
    }
    return browser;
}

// Route de santé
app.get('/health', (req, res) => {
    res.json({ 
        status: 'healthy',
        service: 'PDF Microservice',
        version: '2.0',
        uptime: process.uptime()
    });
});

// Route principale de génération PDF
app.post('/generate-pdf', async (req, res) => {
    let page = null;
    
    try {
        const { html, options = {} } = req.body;
        
        if (!html) {
            return res.status(400).json({ error: 'HTML content is required' });
        }

        // Initialiser le browser si nécessaire
        const browser = await initBrowser();
        
        // Créer une nouvelle page
        page = await browser.newPage();
        
        // Configuration de la page
        await page.setViewport({
            width: 1920,
            height: 1080,
            deviceScaleFactor: 2
        });

        // CSS additionnel pour impression
        const printCSS = `
            <style>
                @media print {
                    * {
                        -webkit-print-color-adjust: exact !important;
                        print-color-adjust: exact !important;
                        color-adjust: exact !important;
                    }
                }
                body {
                    -webkit-font-smoothing: antialiased;
                    -moz-osx-font-smoothing: grayscale;
                }
            </style>
        `;

        // Injecter le HTML avec CSS d'impression
        const enhancedHTML = html.replace('</head>', `${printCSS}</head>`);
        
        // Charger le HTML
        await page.setContent(enhancedHTML, {
            waitUntil: options.waitUntil || 'networkidle0',
            timeout: 30000
        });

        // Options PDF par défaut avec améliorations enterprise
        const pdfOptions = {
            format: options.format || 'A4',
            printBackground: options.printBackground !== false,
            displayHeaderFooter: options.displayHeaderFooter || false,
            headerTemplate: options.headerTemplate || '',
            footerTemplate: options.footerTemplate || '',
            margin: options.margin || {
                top: '15mm',
                right: '10mm',
                bottom: '15mm',
                left: '10mm'
            },
            scale: options.scale || 1,
            landscape: options.landscape || false,
            preferCSSPageSize: options.preferCSSPageSize || false,
            pageRanges: options.pageRanges || '',
        };

        // Émuler le média d'impression
        if (options.emulateMediaType === 'print') {
            await page.emulateMediaType('print');
        }

        // Générer le PDF
        const pdfBuffer = await page.pdf(pdfOptions);

        // Fermer la page
        await page.close();

        // Envoyer le PDF
        res.set({
            'Content-Type': 'application/pdf',
            'Content-Length': pdfBuffer.length,
            'Cache-Control': 'no-cache'
        });

        res.send(pdfBuffer);
        
        console.log(`✅ PDF généré avec succès - Taille: ${(pdfBuffer.length / 1024).toFixed(2)} KB`);

    } catch (error) {
        console.error('❌ Erreur génération PDF:', error);
        
        if (page) {
            await page.close().catch(console.error);
        }
        
        res.status(500).json({ 
            error: 'PDF generation failed',
            message: error.message,
            stack: process.env.NODE_ENV === 'development' ? error.stack : undefined
        });
    }
});

// Route alternative pour compatibilité
app.post('/generate', async (req, res) => {
    // Rediriger vers la nouvelle route
    return app._router.handle(Object.assign(req, { url: '/generate-pdf' }), res);
});

// Gestion de l'arrêt gracieux
process.on('SIGTERM', async () => {
    console.log('⚠️ SIGTERM reçu, fermeture gracieuse...');
    if (browser) {
        await browser.close();
    }
    process.exit(0);
});

process.on('SIGINT', async () => {
    console.log('⚠️ SIGINT reçu, fermeture gracieuse...');
    if (browser) {
        await browser.close();
    }
    process.exit(0);
});

// Démarrage du serveur
app.listen(PORT, async () => {
    console.log(`🚀 PDF Microservice Enterprise démarré sur le port ${PORT}`);
    console.log(`📍 Endpoints disponibles:`);
    console.log(`   - GET  /health`);
    console.log(`   - POST /generate-pdf`);
    console.log(`   - POST /generate (alias)`);
    
    // Pré-initialiser le browser pour des performances optimales
    await initBrowser();
});

export default app;
