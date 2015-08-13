$<?= $variableName; ?> = new <?= $className; ?>(<?= implode(', ', $constructorArgs); ?>);
<?php foreach ($properties as $propertyName => $property): ?>
    $<?= $variableName; ?>-><?= $propertyName; ?> = $property;
<?php endforeach; ?>
<?php foreach ($methods as $methodName => $arguments): ?>
    $<?= $variableName; ?>-><?= $methodName; ?>(<?= implode(', ', $arguments) ?>);
<?php endforeach; ?>
$data[] = $<?= $variableName; ?>;
