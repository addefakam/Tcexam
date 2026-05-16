<?php
require_once('../config/tce_config.php');
$thispage_title = "AI Question Converter";
$pagelevel = 10; // Admin only
require_once('../../shared/code/tce_authorization.php');
require_once('tce_page_header.php');

// Simple pattern-based parser
function parseWordText($text) {
    $questions = [];
    $lines = explode("\n", $text);
    $currentQuestion = null;
    
    foreach ($lines as $line) {
        $line = trim($line);
        if (empty($line)) continue;
        
        // 1. Check if it's an Answer line (A) B. etc)
        if (preg_match('/^[\(\[\s]*([A-Z0-9])[\.\)\s\]]+\s*(.*)$/i', $line, $matches)) {
            if ($currentQuestion !== null) {
                $is_correct = (strpos($line, '*') !== false || preg_match('/\b(correct|true|yes)\b/i', $line));
                $answer_text = trim(preg_replace('/\b(correct|true|yes)\b/i', '', str_replace('*', '', $matches[2])));
                
                $currentQuestion['answers'][] = [
                    'text' => $answer_text,
                    'is_correct' => $is_correct
                ];
                continue;
            }
        }
        
        // 2. Check if it's a new Question line (starts with 1. or 1) or Question 1:)
        if (preg_match('/^(\d+[\.\)]|Question\s*\d+[:\.]?)\s*(.*)$/i', $line, $matches)) {
            // Save previous question if it has answers
            if ($currentQuestion !== null && !empty($currentQuestion['answers'])) {
                $questions[] = $currentQuestion;
            }
            
            $currentQuestion = [
                'text' => trim($matches[2]),
                'answers' => []
            ];
            continue;
        }
        
        // 3. If it doesn't match above, it might be a multi-line question text or description
        if ($currentQuestion !== null && empty($currentQuestion['answers'])) {
            $currentQuestion['text'] .= " " . $line;
        }
    }
    
    // Add the last question
    if ($currentQuestion !== null && !empty($currentQuestion['answers'])) {
        $questions[] = $currentQuestion;
    }
    
    return $questions;
}

$xml_output = "";
if (isset($_POST['convert']) && !empty($_POST['raw_text'])) {
    $parsed = parseWordText($_POST['raw_text']);
    
    $xml_output = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
    $xml_output .= '<tcexamquestion xml:lang="en">' . "\n";
    $xml_output .= '    <header>' . "\n";
    $xml_output .= '        <name>AI Imported Questions</name>' . "\n";
    $xml_output .= '        <description>Imported on ' . date('Y-m-d') . '</description>' . "\n";
    $xml_output .= '    </header>' . "\n";
    $xml_output .= '    <body>' . "\n";
    
    foreach ($parsed as $q) {
        $xml_output .= '        <question type="1" difficulty="1">' . "\n";
        $xml_output .= '            <text>' . htmlspecialchars($q['text']) . '</text>' . "\n";
        foreach ($q['answers'] as $a) {
            $xml_output .= '            <answer is_correct="' . ($a['is_correct'] ? 'true' : 'false') . '">' . htmlspecialchars($a['text']) . '</answer>' . "\n";
        }
        $xml_output .= '        </question>' . "\n";
    }
    
    $xml_output .= '    </body>' . "\n";
    $xml_output .= '</tcexamquestion>';
}
?>

<div class="container" style="max-width: 1000px; margin: 40px auto; font-family: 'Inter', sans-serif;">
    <div style="background: white; padding: 40px; border-radius: 24px; box-shadow: 0 20px 40px rgba(0,0,0,0.05); border: 1px solid rgba(0,0,0,0.05);">
        <div style="display: flex; align-items: center; gap: 20px; margin-bottom: 30px;">
            <div style="background: #4f46e5; color: white; width: 50px; height: 50px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 24px;">✨</div>
            <div>
                <h1 style="margin: 0; font-size: 1.8rem; color: #1e293b;">AI Question Converter</h1>
                <p style="margin: 5px 0 0; color: #64748b;">Convert Word documents to TCExam XML format instantly.</p>
            </div>
        </div>

        <form method="post">
            <div style="margin-bottom: 25px;">
                <label style="display: block; margin-bottom: 10px; font-weight: 600; color: #475569;">Paste Questions from Word</label>
                <p style="font-size: 0.85rem; color: #94a3b8; margin-bottom: 10px;">Example: 1. Question? A) Op 1 B) Op 2* C) Op 3 (Use * to mark the correct answer)</p>
                <textarea name="raw_text" style="width: 100%; height: 250px; padding: 15px; border-radius: 12px; border: 1px solid #e2e8f0; font-family: monospace; font-size: 0.9rem; background: #f8fafc;" placeholder="1. What is the basic unit of life?
A. Cell*
B. Atom
C. Molecule"><?php echo isset($_POST['raw_text']) ? htmlspecialchars($_POST['raw_text']) : ''; ?></textarea>
            </div>

            <button type="submit" name="convert" style="background: #4f46e5; color: white; border: none; padding: 14px 28px; border-radius: 12px; font-weight: 600; cursor: pointer; transition: all 0.3s ease; display: flex; align-items: center; gap: 10px;">
                <span>Convert to XML</span>
            </button>
        </form>

        <?php if (!empty($xml_output)): ?>
            <div style="margin-top: 40px; padding: 25px; background: #f0fdf4; border-radius: 16px; border: 1px solid #bbf7d0;">
                <h3 style="margin-top: 0; color: #166534; display: flex; align-items: center; gap: 10px;">✅ XML Generated Successfully!</h3>
                <p style="font-size: 0.9rem; color: #166534; margin-bottom: 15px;">Copy the code below and save it as a <b>.xml</b> file, then upload it in "Subjects Management".</p>
                <textarea readonly style="width: 100%; height: 200px; padding: 15px; border-radius: 12px; border: 1px solid #86efac; font-family: monospace; font-size: 0.85rem; background: #ffffff;"><?php echo htmlspecialchars($xml_output); ?></textarea>
                
                <div style="margin-top: 15px; display: flex; gap: 10px;">
                    <button onclick="copyToClipboard()" style="background: #166534; color: white; border: none; padding: 10px 20px; border-radius: 8px; font-size: 0.85rem; cursor: pointer;">Copy Code</button>
                </div>
            </div>
            
            <script>
            function copyToClipboard() {
                const textarea = document.querySelector('textarea[readonly]');
                textarea.select();
                document.execCommand('copy');
                alert('XML Code Copied to Clipboard!');
            }
            </script>
        <?php endif; ?>
    </div>
</div>

<style>
    button:hover { transform: translateY(-2px); box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1); filter: brightness(1.1); }
</style>

<?php
require_once('tce_page_footer.php');
?>
