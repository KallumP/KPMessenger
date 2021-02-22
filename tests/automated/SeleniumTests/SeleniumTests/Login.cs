using NUnit.Framework;
using OpenQA.Selenium;
using OpenQA.Selenium.Chrome;
using OpenQA.Selenium.Support.UI;
using System;

namespace SeleniumTests {
    static class Login {

        static IWebDriver browser;

        public static void Setup() {

            var options = new ChromeOptions();

            options.AcceptInsecureCertificates = true;
            options.AddArgument("--headless");

            browser = new ChromeDriver(options);

            browser.Navigate().GoToUrl("https://localhost");
            new WebDriverWait(browser, TimeSpan.FromSeconds(10)).Until(c => c.FindElement(By.CssSelector(".Login")));
        }

        [Test]
        public static void zCorrectCredentials() {
            
            Setup();

            TestHelper.SetText(browser, "css", ".username_txt", "Test 1");
            Console.WriteLine("Correct Username Entered");
            TestHelper.SetText(browser, "css", ".password_txt", "test");
            Console.WriteLine("Correct Password Entered");
            TestHelper.ClickElement(browser, "css", ".login_btn");
            Console.WriteLine("Login button clicked");

            string redirectedURL = browser.Url;
            string expectedRedirect = "https://localhost/KPMessenger/site/index.php";

            TestHelper.Assert(redirectedURL, expectedRedirect);

            browser.Close();
        }

        [Test]
        public static void BadUsername() {

            Setup();

            TestHelper.SetText(browser, "css", ".username_txt", "NotAUserName");
            Console.WriteLine("Bad username Entered");
            TestHelper.SetText(browser, "css", ".password_txt", "Wrong password");
            Console.WriteLine("Bad password entered");
            TestHelper.ClickElement(browser, "css", ".login_btn");
            Console.WriteLine("Login button clicked");

            string errorMessage = TestHelper.GetInterText(browser, "css", "h3");
            string expectedMessage = "That username was wrong";

            TestHelper.Assert(errorMessage, expectedMessage);

            browser.Close();
        }

        [Test]
        public static void BadPassword() {

            Setup();

            TestHelper.SetText(browser, "css", ".username_txt", "Test 1");
            Console.WriteLine("Correct username Entered");
            TestHelper.SetText(browser, "css", ".password_txt", "Wrong password");
            Console.WriteLine("Bad password entered");
            TestHelper.ClickElement(browser, "css", ".login_btn");
            Console.WriteLine("Login button clicked");

            string errorMessage = TestHelper.GetInterText(browser, "css", "h3");
            string expectedMessage = "That password was wrong";

            TestHelper.Assert(errorMessage, expectedMessage);

            browser.Close();
        }

        [Test]
        public static void CorrectUsernameDifferentUsersPassword() {

            Setup();

            TestHelper.SetText(browser, "css", ".username_txt", "Test 1");
            Console.WriteLine("Correct username Entered");
            TestHelper.SetText(browser, "css", ".password_txt", "admin");
            Console.WriteLine("Password for user Test 2 entered");
            TestHelper.ClickElement(browser, "css", ".login_btn");
            Console.WriteLine("Login button clicked");

            string errorMessage = TestHelper.GetInterText(browser, "css", "h3");
            string expectedMessage = "That password was wrong";

            TestHelper.Assert(errorMessage, expectedMessage);

            browser.Close();
        }

        [Test]
        public static void NoUsername() {

            Setup();

            TestHelper.SetText(browser, "css", ".password_txt", "admin");
            Console.WriteLine("Password for user Test 2 entered");
            TestHelper.ClickElement(browser, "css", ".login_btn");
            Console.WriteLine("Login button clicked");

            string errorMessage = TestHelper.GetInterText(browser, "css", "h3");
            string expectedMessage = "Please fill out both text boxes";

            TestHelper.Assert(errorMessage, expectedMessage);

            browser.Close();
        }

        [Test]
        public static void NoPassword() {

            Setup();

            TestHelper.SetText(browser, "css", ".username_txt", "Test 1");
            Console.WriteLine("Correct username Entered");
            TestHelper.ClickElement(browser, "css", ".login_btn");
            Console.WriteLine("Login button clicked");

            string errorMessage = TestHelper.GetInterText(browser, "css", "h3");
            string expectedMessage = "Please fill out both text boxes";

            TestHelper.Assert(errorMessage, expectedMessage);

            browser.Close();
        }

        [Test]
        public static void NoInputs() {
            
            Setup();

            TestHelper.ClickElement(browser, "css", ".login_btn");
            Console.WriteLine("Login button clicked");

            string errorMessage = TestHelper.GetInterText(browser, "css", "h3");
            string expectedMessage = "Please fill out both text boxes";

            TestHelper.Assert(errorMessage, expectedMessage);

            browser.Close();
        }

    }
}
