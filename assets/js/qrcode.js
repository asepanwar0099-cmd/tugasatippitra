// qrcode.js - QR Code generator utilities
function downloadQr() {
    const canvas = document.querySelector('#qrcode canvas');
    const img = document.querySelector('#qrcode img');
    const link = document.createElement('a');
    const qrPayload = document.getElementById('qrcode')?.querySelector('canvas')?.parentElement?.id || 'qr-code';
    
    link.download = `qr-dosen-${new Date().getTime()}.png`;
    link.href = canvas ? canvas.toDataURL('image/png') : img.src;
    link.click();
}

function printQr() {
    const printArea = document.getElementById('printArea');
    if (!printArea) return;

    const printWindow = window.open('', '', 'width=400,height=600');
    printWindow.document.write(`
        <!DOCTYPE html>
        <html>
        <head>
            <title>Print QR Code</title>
            <style>
                body { font-family: Arial; text-align: center; padding: 20px; }
                .qr-card { padding: 30px; }
                img, canvas { max-width: 100%; }
                @media print { body { margin: 0; } }
            </style>
        </head>
        <body>
            ${printArea.innerHTML}
            <script>
                window.print();
                window.close();
            </script>
        </body>
        </html>
    `);
    printWindow.document.close();
}
