param(
  [Parameter(ValueFromRemainingArguments = $true)]
  [string[]]$Arguments
)

docker compose run --rm wpcli @Arguments

