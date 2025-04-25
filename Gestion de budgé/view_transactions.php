<?php
session_start();
require_once 'config.php';
require_once 'user.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['user_id'];

// Traitement des formulaires
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['delete_id'])) {
        supprimerTransaction($pdo, $_POST['delete_id'], $user_id);
        header('Location: ' . $_SERVER['PHP_SELF']);
        exit;
    }
}

// Récupération des données
$stmt = $pdo->prepare("SELECT t.id, t.montant, t.description, t.date_transaction, c.nom AS categorie
                       FROM transactions t
                       JOIN categories c ON t.category_id = c.id
                       WHERE t.user_id = ? 
                       ORDER BY t.date_transaction DESC");
$stmt->execute([$user_id]);
$transactions = $stmt->fetchAll(PDO::FETCH_ASSOC);

$stmt_solde = $pdo->prepare("SELECT SUM(montant) AS total FROM transactions WHERE user_id = ?");
$stmt_solde->execute([$user_id]);
$solde = $stmt_solde->fetch(PDO::FETCH_ASSOC)['total'];

$mois_en_cours = date('Y-m');
$stmt_revenu_depense = $pdo->prepare("SELECT 
        SUM(CASE WHEN montant > 0 THEN montant ELSE 0 END) AS total_revenus, 
        SUM(CASE WHEN montant < 0 THEN montant ELSE 0 END) AS total_depenses
    FROM transactions
    WHERE user_id = ? AND DATE_FORMAT(date_transaction, '%Y-%m') = ?");
$stmt_revenu_depense->execute([$user_id, $mois_en_cours]);
$resume_mois = $stmt_revenu_depense->fetch(PDO::FETCH_ASSOC);

$stmt_categorie = $pdo->prepare("SELECT c.nom, 
        SUM(CASE WHEN t.montant > 0 THEN t.montant ELSE 0 END) AS total_revenus,
        SUM(CASE WHEN t.montant < 0 THEN t.montant ELSE 0 END) AS total_depenses
    FROM transactions t
    JOIN categories c ON t.category_id = c.id
    WHERE t.user_id = ?
    GROUP BY c.nom");
$stmt_categorie->execute([$user_id]);
$categories = $stmt_categorie->fetchAll(PDO::FETCH_ASSOC);

$stmt_max = $pdo->prepare("SELECT 
        MAX(montant) AS max_revenu, 
        MIN(montant) AS max_depense
    FROM transactions
    WHERE user_id = ? AND DATE_FORMAT(date_transaction, '%Y-%m') = ?");
$stmt_max->execute([$user_id, $mois_en_cours]);
$max_values = $stmt_max->fetch(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Voir les Transactions</title>
   <link rel="stylesheet" href="transaction.css?v=1">
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
<nav class="navbar">
        <div class="navbar-brand">
            <h1>Budget Manager</h1>
        </div>
        <div class="navbar-links">
            <a href="ajouter_transaction.php" class="active">Ajouter Transaction</a>
            <a href="view_transactions.php">Voir Transactions</a>
            <a href="login.php">se connecter</a>
        </div>
    </nav>
    <div class="container">
        <div class="header">
            <h1><i class="fas fa-coins"></i> Transactions</h1>
            <a href="ajouter_transaction.php" class="btn">
                <i class="fas fa-plus"></i> Ajouter une transaction
            </a>
        </div>

        <div class="dashboard">
            <h2 class="section-title"><i class="fas fa-chart-line"></i> Informations financières</h2>
            
            <div class="cards">
                <div class="card card-solde">
                    <h3 class="card-title"><i class="fas fa-wallet"></i> Solde actuel</h3>
                    <div class="card-value"><?= number_format($solde, 2, ',', ' ') ?> €</div>
                </div>
                
                <div class="card card-revenus">
                    <h3 class="card-title"><i class="fas fa-arrow-up"></i> Total des revenus</h3>
                    <div class="card-value"><?= number_format($resume_mois['total_revenus'], 2, ',', ' ') ?> €</div>
                </div>
                
                <div class="card card-depenses">
                    <h3 class="card-title"><i class="fas fa-arrow-down"></i> Total des dépenses</h3>
                    <div class="card-value"><?= number_format(abs($resume_mois['total_depenses']), 2, ',', ' ') ?> €</div>
                </div>
                
                <div class="card card-max-depense">
                    <h3 class="card-title"><i class="fas fa-exclamation-triangle"></i> Dépense la plus haute</h3>
                    <div class="card-value"><?= number_format(abs($max_values['max_depense']), 2, ',', ' ') ?> €</div>
                </div>
                
                <div class="card card-max-revenu">
                    <h3 class="card-title"><i class="fas fa-trophy"></i> Revenu le plus élevé</h3>
                    <div class="card-value"><?= number_format($max_values['max_revenu'], 2, ',', ' ') ?> €</div>
                </div>
            </div>

            <h2 class="section-title"><i class="fas fa-tags"></i> Somme des revenus et dépenses par catégorie</h2>
            
            <div class="table-wrapper">
                <table class="categories">
                    <thead>
                        <tr>
                            <th><i class="fas fa-folder"></i> Catégorie</th>
                            <th><i class="fas fa-plus-circle"></i> Total des Revenus</th>
                            <th><i class="fas fa-minus-circle"></i> Total des Dépenses</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($categories as $categorie): ?>
                            <tr>
                                <td><?= htmlspecialchars($categorie['nom']) ?></td>
                                <td class="amount-positive"><?= number_format($categorie['total_revenus'], 2, ',', ' ') ?> €</td>
                                <td class="amount-negative"><?= number_format(abs($categorie['total_depenses']), 2, ',', ' ') ?> €</td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <h2 class="section-title"><i class="fas fa-list"></i> Liste des transactions</h2>
        
        <?php if (empty($transactions)): ?>
            <div class="empty-state">
                <i class="fas fa-search"></i>
                <p>Aucune transaction trouvée.</p>
            </div>
        <?php else: ?>
            <div class="table-wrapper">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Montant</th>
                            <th>Catégorie</th>
                            <th>Description</th>
                            <th>Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($transactions as $transaction): ?>
                            <tr>
                                <td><?= htmlspecialchars($transaction['id']) ?></td>
                                <td class="<?= $transaction['montant'] > 0 ? 'amount-positive' : 'amount-negative' ?>">
                                    <?= number_format($transaction['montant'], 2, ',', ' ') ?> €
                                </td>
                                <td>
                                    <i class="fas fa-tag"></i> <?= htmlspecialchars($transaction['categorie']) ?>
                                </td>
                                <td><?= htmlspecialchars($transaction['description']) ?></td>
                                <td>
                                    <i class="far fa-calendar-alt"></i> <?= date('d/m/Y', strtotime($transaction['date_transaction'])) ?>
                                </td>
                                <td>
                                    <a href="modifier_transaction.php?id=<?= $transaction['id'] ?>" class="action-btn edit-btn" title="Modifier">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form method="post" style="display:inline;">
                                        <input type="hidden" name="delete_id" value="<?= $transaction['id'] ?>">
                                        <button type="submit" class="action-btn delete-btn" title="Supprimer" onclick="return confirm('Supprimer cette transaction ?')">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
    <footer class="footer">
        <p>© 2025 Budget Manager. Tous droits réservés. <a href="mailto:contact@budgetmanager.com">Nous contacter</a></p>
    </footer>
</body>
</html>