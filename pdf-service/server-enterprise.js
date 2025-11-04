/**
 * PDF Microservice - ZenFleet Enterprise v3.0
 * Service haute performance pour génération PDF premium
 */

const express = require('express');
const puppeteer = require('puppeteer');
const app = express();
const PORT = process.env.PORT || 3000;

// Middleware
app.use(express.json({ limit: '50mb' }));
app.use(express.urlencoded({ limit: '50mb', extended: true }));
app.use((req, res, next) => {
    res.header('Access-Control-Allow-Origin', '*');
    res.header('Access-Control-Allow-Methods', 'GET,POST,OPTIONS');
    res.header('Access-Control-Allow-Headers', 'Content-Type');
    next();
});

let browser = null;

// Initialisation du browser Puppeteer
async function initBrowser() {
    if (!browser) {
        console.log('🚀 Initialisation de Puppeteer...');
        try {
            browser = await puppeteer.launch({
                headless: 'new',
                args: [
                    '--no-sandbox',
                    '--disable-setuid-sandbox',
                    '--disable-dev-shm-usage',
                    '--disable-gpu',
                    '--disable-web-security',
                    '--disable-features=IsolateOrigins,site-per-process',
                    '--window-size=1920,1080'
                ],
                defaultViewport: {
                    width: 1920,
                    height: 1080
                }
            });
            console.log('✅ Browser Puppeteer prêt');
        } catch (error) {
            console.error('❌ Erreur initialisation Puppeteer:', error);
            throw error;
        }
    }
    return browser;
}

// Health check
app.get('/health', (req, res) => {
    res.json({ 
        status: 'healthy',
        service: 'PDF Microservice Enterprise',
        version: '3.0',
        uptime: process.uptime(),
        memory: process.memoryUsage(),
        timestamp: new Date().toISOString()
    });
});

// Route principale de génération PDF
app.post('/generate-pdf', async (req, res) => {
    console.log('📄 Nouvelle demande de génération PDF reçue');
    let page = null;
    
    try {
        const { html, options = {} } = req.body;
        
        if (!html) {
            console.error('❌ HTML manquant dans la requête');
            return res.status(400).json({ 
                error: 'HTML content is required',
                message: 'Le contenu HTML est obligatoire' 
            });
        }

        console.log('📝 HTML reçu, taille:', html.length, 'caractères');

        // S'assurer que le browser est initialisé
        const browserInstance = await initBrowser();
        
        // Créer une nouvelle page
        console.log('📄 Création d\'une nouvelle page...');
        page = await browserInstance.newPage();
        
        // Configuration de la page
        await page.setViewport({
            width: 1920,
            height: 1080,
            deviceScaleFactor: 2
        });

        // CSS pour améliorer le rendu PDF
        const enhancedCSS = `
            <style>
                @media print {
                    * {
                        -webkit-print-color-adjust: exact !important;
                        print-color-adjust: exact !important;
                        color-adjust: exact !important;
                    }
                    body {
                        margin: 0;
                        padding: 0;
                    }
                }
                body {
                    -webkit-font-smoothing: antialiased;
                    -moz-osx-font-smoothing: grayscale;
                    text-rendering: optimizeLegibility;
                }
            </style>
        `;

        // Injecter le CSS et le HTML
        const finalHTML = html.replace('</head>', `${enhancedCSS}</head>`);
        
        console.log('⏳ Chargement du HTML dans la page...');
        await page.setContent(finalHTML, {
            waitUntil: options.waitUntil || 'networkidle0',
            timeout: 30000
        });

        console.log('✅ HTML chargé avec succès');

        // Options PDF optimisées
        const pdfOptions = {
            format: options.format || 'A4',
            printBackground: options.printBackground !== false,
            displayHeaderFooter: options.displayHeaderFooter || false,
            headerTemplate: options.headerTemplate || '<div></div>',
            footerTemplate: options.footerTemplate || '<div></div>',
            margin: options.margin || {
                top: '15mm',
                right: '10mm',
                bottom: '15mm',
                left: '10mm'
            },
            scale: options.scale || 1,
            landscape: options.landscape || false,
            preferCSSPageSize: false,
            pageRanges: options.pageRanges || ''
        };

        // Émuler le média d'impression si demandé
        if (options.emulateMediaType === 'print') {
            await page.emulateMediaType('print');
        }

        console.log('🖨️ Génération du PDF avec options:', JSON.stringify(pdfOptions));
        
        // Générer le PDF
        const pdfBuffer = await page.pdf(pdfOptions);

        console.log('✅ PDF généré avec succès, taille:', Math.round(pdfBuffer.length / 1024), 'KB');

        // Fermer la page pour libérer la mémoire
        await page.close();
        page = null;

        // Headers de réponse pour forcer le téléchargement
        res.set({
            'Content-Type': 'application/pdf',
            'Content-Length': pdfBuffer.length,
            'Cache-Control': 'no-cache, no-store, must-revalidate',
            'Pragma': 'no-cache',
            'Expires': '0'
        });

        // Envoyer le PDF
        res.send(pdfBuffer);
        
    } catch (error) {
        console.error('❌ Erreur lors de la génération du PDF:', error);
        console.error('Stack:', error.stack);
        
        // Nettoyer la page en cas d'erreur
        if (page) {
            try {
                await page.close();
            } catch (closeError) {
                console.error('Erreur fermeture page:', closeError);
            }
        }
        
        res.status(500).json({ 
            error: 'PDF generation failed',
            message: error.message,
            details: process.env.NODE_ENV === 'development' ? error.stack : undefined
        });
    }
});

// Route alias pour compatibilité
app.post('/generate', async (req, res) => {
    console.log('🔄 Redirection /generate vers /generate-pdf');
    req.url = '/generate-pdf';
    app.handle(req, res);
});

// Test route pour vérifier que le service fonctionne
app.get('/test', (req, res) => {
    res.send(`
        <!DOCTYPE html>
        <html>
        <head><title>Test PDF Service</title></head>
        <body>
            <h1>PDF Service Enterprise v3.0</h1>
            <p>Status: ✅ Opérationnel</p>
            <p>Uptime: ${Math.round(process.uptime() / 60)} minutes</p>
            <p>Memory: ${Math.round(process.memoryUsage().heapUsed / 1024 / 1024)} MB</p>
        </body>
        </html>
    `);
});

// Gestion des erreurs non capturées
process.on('unhandledRejection', (reason, promise) => {
    console.error('❌ Unhandled Rejection at:', promise, 'reason:', reason);
});

process.on('uncaughtException', (error) => {
    console.error('❌ Uncaught Exception:', error);
    process.exit(1);
});

// Gestion de l'arrêt gracieux
process.on('SIGTERM', async () => {
    console.log('⚠️ SIGTERM reçu, fermeture gracieuse...');
    if (browser) {
        await browser.close();
        console.log('✅ Browser fermé');
    }
    process.exit(0);
});

process.on('SIGINT', async () => {
    console.log('⚠️ SIGINT reçu, fermeture gracieuse...');
    if (browser) {
        await browser.close();
        console.log('✅ Browser fermé');
    }
    process.exit(0);
});

// Démarrage du serveur
app.listen(PORT, async () => {
    console.log('');
    console.log('========================================');
    console.log('🚀 PDF Microservice Enterprise v3.0');
    console.log('========================================');
    console.log(`📍 Port: ${PORT}`);
    console.log(`📍 Environment: ${process.env.NODE_ENV || 'production'}`);
    console.log(`📍 Process ID: ${process.pid}`);
    console.log('');
    console.log('📌 Endpoints disponibles:');
    console.log(`   GET  http://localhost:${PORT}/health`);
    console.log(`   GET  http://localhost:${PORT}/test`);
    console.log(`   POST http://localhost:${PORT}/generate-pdf`);
    console.log(`   POST http://localhost:${PORT}/generate`);
    console.log('========================================');
    console.log('');
    
    // Initialiser le browser au démarrage pour des performances optimales
    try {
        await initBrowser();
        console.log('✅ Service totalement opérationnel');
    } catch (error) {
        console.error('❌ Erreur lors de l\'initialisation:', error);
        process.exit(1);
    }
});
