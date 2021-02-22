using NUnit.Framework;
using OpenQA.Selenium;
using OpenQA.Selenium.Chrome;
using OpenQA.Selenium.Support.UI;
using System;
using System.Threading;

namespace SeleniumTests {
    static class NewAccount {

        static IWebDriver browser;

        public static bool Setup() {

            ChromeOptions options = new ChromeOptions();

            options.AcceptInsecureCertificates = true;
            options.AddArgument("--headless");
            browser = new ChromeDriver(options);

            browser.Navigate().GoToUrl("https://localhost");
            new WebDriverWait(browser, TimeSpan.FromSeconds(10)).Until(c => c.FindElement(By.CssSelector(".Login")));

            TestHelper.ClickElement(browser, "css", "p");
            Console.WriteLine("Clicked on 'Create new account'");

            string redirectedURL = browser.Url;
            string expectedRedirect = "https://localhost/KPMessenger/site/createNewAccount.php";


            return TestHelper.CheckFail(redirectedURL, expectedRedirect);
        }

        [Test]
        [Description("Entering a username that is already taken")]
        public static void UsernameAlreadyTaken() {

            if (Setup()) {


                TestHelper.SetText(browser, "css", ".username_txt", "Test 1");
                Console.WriteLine("Username Entered");
                TestHelper.SetText(browser, "name", "password", "test");
                Console.WriteLine("Password Entered");
                TestHelper.SetText(browser, "name", "password-validate", "test");
                Console.WriteLine("Validation password entered");

                TestHelper.ClickElement(browser, "css", ".login_btn");
                Console.WriteLine("Submit button clicked");

                string errorMessage = TestHelper.GetInterText(browser, "css", "h3");
                string expectedMessage = "That username was already taken";

                TestHelper.Assert(errorMessage, expectedMessage);

            }
            browser.Close();
        }

        [Test]
        [Description("Entering a wrong validation password")]
        public static void PasswordsDontMatch() {

            if (Setup()) {

                TestHelper.SetText(browser, "css", ".username_txt", "Test 4");
                Console.WriteLine("Username Entered");
                TestHelper.SetText(browser, "name", "password", "test");
                Console.WriteLine("Password Entered");
                TestHelper.SetText(browser, "name", "password-validate", "differentPass");
                Console.WriteLine("Wrong validation password entered");

                TestHelper.ClickElement(browser, "css", ".login_btn");
                Console.WriteLine("Submit button clicked");

                string errorMessage = TestHelper.GetInterText(browser, "css", "h3");
                string expectedMessage = "The password fields were not the same";

                TestHelper.Assert(errorMessage, expectedMessage);

            }
            browser.Close();
        }

        [Test]
        [Description("Not entering the username")]
        public static void EmptyUserName() {

            if (Setup()) {

                TestHelper.SetText(browser, "name", "password", "test");
                Console.WriteLine("Password Entered");
                TestHelper.SetText(browser, "name", "password-validate", "differentPass");
                Console.WriteLine("Validation password entered");

                TestHelper.ClickElement(browser, "css", ".login_btn");
                Console.WriteLine("Submit button clicked");

                string errorMessage = TestHelper.GetInterText(browser, "css", "h3");
                string expectedMessage = "Please fill out all the fields";

                TestHelper.Assert(errorMessage, expectedMessage);

            }

            browser.Close();
        }

        [Test]
        [Description("Not entering the password")]
        public static void EmptyPassword() {

            if (Setup()) {

                TestHelper.SetText(browser, "css", ".username_txt", "Test 4");
                Console.WriteLine("Username Entered");
                TestHelper.SetText(browser, "name", "password-validate", "differentPass");
                Console.WriteLine("Validation password entered");

                TestHelper.ClickElement(browser, "css", ".login_btn");
                Console.WriteLine("Submit button clicked");

                string errorMessage = TestHelper.GetInterText(browser, "css", "h3");
                string expectedMessage = "Please fill out all the fields";

                TestHelper.Assert(errorMessage, expectedMessage);

            }

            browser.Close();
        }

        [Test]
        [Description("Not entering the validation password")]
        public static void EmptyValidatePassword() {

            if (Setup()) {

                TestHelper.SetText(browser, "css", ".username_txt", "Test 4");
                Console.WriteLine("Username Entered");
                TestHelper.SetText(browser, "name", "password", "test");
                Console.WriteLine("Password Entered");

                TestHelper.ClickElement(browser, "css", ".login_btn");
                Console.WriteLine("Submit button clicked");

                string errorMessage = TestHelper.GetInterText(browser, "css", "h3");
                string expectedMessage = "Please fill out all the fields";

                TestHelper.Assert(errorMessage, expectedMessage);

            }

            browser.Close();
        }

        [Test]
        [Description("Not entering anything")]
        public static void EmptyInputs() {

            if (Setup()) {

                TestHelper.ClickElement(browser, "css", ".login_btn");
                Console.WriteLine("Submit button clicked");

                string errorMessage = TestHelper.GetInterText(browser, "css", "h3");
                string expectedMessage = "Please fill out all the fields";

                TestHelper.Assert(errorMessage, expectedMessage);

            }

            browser.Close();
        }

        [Test]
        [Description("Not entering anything")]
        public static void zCorrectInputs() {

            if (Setup()) {

                TestHelper.SetText(browser, "css", ".username_txt", "Test 4");
                Console.WriteLine("Username Entered");
                TestHelper.SetText(browser, "name", "password", "test");
                Console.WriteLine("Password Entered");
                TestHelper.SetText(browser, "name", "password-validate", "test");
                Console.WriteLine("Correct validation password entered");

                TestHelper.ClickElement(browser, "css", ".login_btn");
                Console.WriteLine("Submit button clicked");

                string redirectedURL = browser.Url;
                string expectedRedirect = "https://localhost/KPMessenger/site/index.php";

                TestHelper.Assert(redirectedURL, expectedRedirect);

            }

            browser.Close();

            DatabaseHelper.RemoveUser("Test 4");
        }
    }
}
