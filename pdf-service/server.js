import express from 'express';
import puppeteer from 'puppeteer';

const app = express();
const PORT = process.env.PORT || 3000;

app.use(express.json({ limit: '10mb' }));

app.get('/health', (req, res) => {
    res.status(200).json({ status: 'OK' });
});

app.post('/generate-pdf', async (req, res) => {
    const { html, options = {} } = req.body;

    if (!html) {
        return res.status(400).json({ error: 'Le contenu HTML est manquant.' });
    }

    let browser = null;
    try {
        console.log('Lancement du navigateur...');
        // Puppeteer trouvera automatiquement le navigateur installé dans le conteneur
        browser = await puppeteer.launch({
            headless: true,
            args: [
                '--no-sandbox',
                '--disable-setuid-sandbox',
                '--disable-dev-shm-usage',
                '--disable-gpu',
            ],
            executablePath: puppeteer.executablePath()
        });

        const page = await browser.newPage();
        await page.setContent(html, { waitUntil: 'networkidle0' });

        const pdfBuffer = await page.pdf({ format: 'A4', printBackground: true, ...options });
        console.log(`PDF généré avec succès (${(pdfBuffer.length / 1024).toFixed(2)} KB)`);

        res.setHeader('Content-Type', 'application/pdf');
        res.setHeader('Content-Disposition', 'attachment; filename="document.pdf"');
        res.send(pdfBuffer);

    } catch (error) {
        console.error('Erreur lors de la génération du PDF:', error);
        res.status(500).json({ error: 'Erreur serveur lors de la génération du PDF.', details: error.message });
    } finally {
        if (browser) await browser.close();
    }
});

app.listen(PORT, '0.0.0.0', () => {
    console.log(`🚀 Service PDF ZenFleet démarré sur le port ${PORT}`);
});